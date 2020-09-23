<?php namespace App\Http\Controllers;

use App\User;
use App\Profit;
use App\Battle;
use App\BattleBets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DB;

class BattleController extends Controller {
	
    public function __construct() {
		parent::__construct();
        $this->game = $this->getLastGame();
		view()->share('game', $this->game);
		view()->share('time', $this->getTime());
		DB::connection()->getPdo()->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    }
	
	public function getLastGame() {
        $game = Battle::orderBy('id', 'desc')->first();
        if(is_null($game)) $game = Battle::create([
			'hash' => bin2hex(random_bytes(16))
		]);
        return $game;
    }
	
	private function getBets() {
        $bets = BattleBets::where('battle_bets.game_id', $this->game->id)
				->select('battle_bets.user_id', DB::raw('SUM(battle_bets.price) as price'), 'users.username', 'users.avatar', 'users.unique_id', 'battle_bets.color', 'battle_bets.balType')
				->join('users', 'users.id', '=', 'battle_bets.user_id')
				->groupBy('battle_bets.user_id', 'battle_bets.color', 'battle_bets.balType')
				->orderBy('price', 'desc')
				->get();
        return $bets;
    }
	
	public function index() {
        $bets = $this->getBets();
		$factor = [$this->getXForGame($this->game, 'red'), $this->getXForGame($this->game, 'blue')];
		$bank = [$this->getBankForGame($this->game, 'red'), $this->getBankForGame($this->game, 'blue')];
		$chances = [$this->getChanceOfColor('red', $this->game), $this->getChanceOfColor('blue', $this->game)];
		$count = [$this->getCountOfColor('red', $this->game), $this->getCountOfColor('blue', $this->game)];
		$tickets = $this->getTicketsOfGame($this->game);
		$lastwins = Battle::orderBy('id', 'desc')->where('status', Battle::STATUS_FINISHED)->limit(15)->get();
        return view('pages.battle', compact('bets', 'factor', 'bank', 'chances', 'tickets', 'factor', 'lastwins', 'count'));
	}
	
	public function newGame() {
		$game = Battle::create([
			'hash' => bin2hex(random_bytes(16))
		]);
        $this->redis->set('current.game', $game->id);
		
		return response()->json([
			'game' => $game,
			'time' => $this->getTime()
        ]);
	}
	
	public function newBet(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		if($this->user->ban) return;
		
		$color = $r->get('type');
		$balType = $r->get('balance');
		$sum = round(preg_replace('/[^.0-9]/', '', $r->get('sum')), 2) ?? null;
		
		if($balType != 'balance' && $balType != 'bonus') return response()->json(['type' => 'error', 'msg' => 'Не удалось определить тип Вашего баланса!']);
		if($balType == 'balance' && $this->user->balance < $sum) return response()->json(['type' => 'error', 'msg' => 'Недостаточно баланса!']);
		if($balType == 'bonus' && $this->user->bonus < $sum) return response()->json(['type' => 'error', 'msg' => 'Недостаточно баланса!']);
		if(is_null($color) || $color != 'red' && $color != 'blue') return response()->json(['msg' => 'Цвет не найден', 'type' => 'error']);
		if(is_null($sum)) return response()->json(['msg' => 'Вы ввели неверную сумму', 'type' => 'error']);
		if($sum < $this->settings->battle_min_bet) return response()->json(['msg' => 'Минимальная сумма ставки '.$this->settings->battle_min_bet.' руб!', 'type' => 'error']);
		if($sum > $this->settings->battle_max_bet) return response()->json(['msg' => 'Максимальная сумма ставки '.$this->settings->battle_max_bet.' руб!', 'type' => 'error']);
		if($this->game->status == Battle::STATUS_PRE_FINISH || $this->game->status == Battle::STATUS_FINISHED) return response()->json(['msg' => 'Игра уже началась или закончилась!', 'type' => 'error']);
		
		$bets = BattleBets::where([
            'user_id' => $this->user->id,
            'game_id' => $this->game->id
        ])->select('color', 'balType')->groupBy('color', 'balType')->get();
		
		$countbets = BattleBets::where('game_id', $this->game->id)->where('user_id', $this->user->id)->count();
		if($countbets >= 3) return response()->json(['msg' => 'Разрешено только 3 ставки!', 'type' => 'error']);
		
		foreach($bets as $b) {
			if($balType != $b->balType) return response()->json(['type' => 'error', 'msg' => 'Вы уже сделали ставку с '. (($balType == 'balance') ? 'бонусного' : 'реального') .' счета!']);
			if($color != $b->color) return response()->json(['type' => 'error', 'msg' => 'Вы уже сделали ставку на другой цвет!']);
		}
		
		DB::beginTransaction();
		try {
			$bet = new BattleBets();
			$bet->user()->associate($this->user);
			$bet->price = $sum;
			$bet->color = $color;
			$bet->game()->associate($this->game);
			$bet->balType = $balType;
			$bet->save();

			$bets = BattleBets::where('game_id', $this->game->id);
			$this->game->price = $bets->sum('price');
		
			if($balType == 'balance') {
				$this->user->balance -= round($sum, 2);
				$this->user->requery += round($sum/100*$this->settings->requery_bet_perc, 3);
				$this->user->save();

				$this->redis->publish('updateBalance', json_encode([
					'unique_id' => $this->user->unique_id,
					'balance' 	=> round($this->user->balance, 2)
				]));
			}

			if($balType == 'bonus') {
				$this->user->bonus -= round($sum, 2);
				$this->user->save();

				$this->redis->publish('updateBonus', json_encode([
					'unique_id' => $this->user->unique_id,
					'bonus' 	=> round($this->user->bonus, 2)
				]));
			}
			
			DB::commit();
		} catch (\PDOException $e) {
			DB::connection()->getPdo()->rollBack();
			return response()->json(['msg' => 'Ошибка сервера!', 'type' => 'error']);
		}
		
		$this->game->save();
		$bets = BattleBets::where('game_id', $this->game->id)->get();

		$this->redis->publish('battle.newBet', json_encode([
			'bank' 		=> [round($this->getBankForGame($this->game, 'red'), 2), round($this->getBankForGame($this->game, 'blue'), 2)],
			'bets' 		=> $this->getBets(),
			'tickets' 	=> $this->getTicketsOfGame($this->game),
			'factor' 	=> [$this->getXForGame($this->game, 'red'), $this->getXForGame($this->game, 'blue')],
			'chances' 	=> [floor($this->getChanceOfColor('red', $this->game)), round($this->getChanceOfColor('blue', $this->game))],
			'count' 	=> [$this->getCountOfColor('red', $this->game), $this->getCountOfColor('blue', $this->game)]
		]));
		
		if($this->getUserInGame()[0] >= 1 && $this->getUserInGame()[1] >= 1) {
			if($this->game->status < Battle::STATUS_PLAYING) {
				$this->game->status = Battle::STATUS_PLAYING;
				$this->game->save();
				$this->startTimer();
			}
        }
		
		return response()->json(['msg' => 'Ваша ставка принята!', 'type' => 'success']);
	}
	
	public function adminBet(Request $r) {
		$user = User::where('user_id', $r->get('user'))->first();
		$color = $r->get('color');
		$balType = $r->get('balance');
		$sum = round(preg_replace('/[^.0-9]/', '', $r->get('sum')), 2) ?? null;
		
		if($balType != 'balance' && $balType != 'bonus') return response()->json(['type' => 'error', 'msg' => 'Не удалось определить тип Вашего баланса!']);
		if(is_null($color) || $color != 'red' && $color != 'blue') return response()->json(['msg' => 'Цвет не найден', 'type' => 'error']);
		if(is_null($sum)) return response()->json(['msg' => 'Вы ввели неверную сумму', 'type' => 'error']);
		if($sum < $this->settings->battle_min_bet) return response()->json(['msg' => 'Минимальная сумма ставки '.$this->settings->battle_min_bet.' руб!', 'type' => 'error']);
		if($sum > $this->settings->battle_max_bet) return response()->json(['msg' => 'Максимальная сумма ставки '.$this->settings->battle_max_bet.' руб!', 'type' => 'error']);
		if($this->game->status == Battle::STATUS_PRE_FINISH || $this->game->status == Battle::STATUS_FINISHED) return response()->json(['msg' => 'Игра уже началась или закончилась!', 'type' => 'error']);
		
		$bets = BattleBets::where([
            'user_id' => $user->id,
            'game_id' => $this->game->id
        ])->select('color', 'balType')->groupBy('color', 'balType')->get();
		
		$countbets = BattleBets::where('game_id', $this->game->id)->where('user_id', $user->id)->count();
		if($countbets >= 3) return response()->json(['msg' => 'Разрешено только 3 ставки!', 'type' => 'error']);
		
		foreach($bets as $b) {
			if($balType != $b->balType) return response()->json(['type' => 'error', 'msg' => 'Вы уже сделали ставку с '. (($balType == 'balance') ? 'бонусного' : 'реального') .' счета!']);
			if($color != $b->color) return response()->json(['type' => 'error', 'msg' => 'Вы уже сделали ставку на другой цвет!']);
		}
		
		DB::beginTransaction();
		try {
			$bet = new BattleBets();
			$bet->user()->associate($user);
			$bet->price = $sum;
			$bet->color = $color;
			$bet->game()->associate($this->game);
			$bet->balType = $balType;
			$bet->fake = 1;
			$bet->save();

			$bets = BattleBets::where('game_id', $this->game->id);
			$this->game->price = $bets->sum('price');
			
			DB::commit();
		} catch (\PDOException $e) {
			DB::connection()->getPdo()->rollBack();
			return response()->json(['msg' => 'Ошибка сервера!', 'type' => 'error']);
		}
		
		$this->game->save();
		$bets = BattleBets::where('game_id', $this->game->id)->get();

		$this->redis->publish('battle.newBet', json_encode([
			'bank' 		=> [round($this->getBankForGame($this->game, 'red'), 2), round($this->getBankForGame($this->game, 'blue'), 2)],
			'bets' 		=> $this->getBets(),
			'tickets' 	=> $this->getTicketsOfGame($this->game),
			'factor' 	=> [$this->getXForGame($this->game, 'red'), $this->getXForGame($this->game, 'blue')],
			'chances' 	=> [floor($this->getChanceOfColor('red', $this->game)), round($this->getChanceOfColor('blue', $this->game))],
			'count' 	=> [$this->getCountOfColor('red', $this->game), $this->getCountOfColor('blue', $this->game)]
		]));
		
		if($this->getUserInGame()[0] >= 1 && $this->getUserInGame()[1] >= 1) {
			if($this->game->status < Battle::STATUS_PLAYING) {
				$this->game->status = Battle::STATUS_PLAYING;
				$this->game->save();
				$this->startTimer();
			}
        }
		
		return response()->json(['msg' => 'Ваша ставка принята!', 'type' => 'success']);
	}
	
	
	public function addBetFake() {
		$user = $this->getUser();
		
		$clrs = ['blue', 'red'];
		$clrs_true = $clrs[array_rand($clrs)];
		$bl = ['balance'];
		$bl_true = $bl[array_rand($bl)];
		$color = $clrs_true;
		$balType = $bl_true;
		$sum = $this->settings->battle_min_bet+mt_rand($this->settings->fake_min_bet * 2, $this->settings->fake_max_bet * 2) / 2;
		
		if($balType != 'balance' && $balType != 'bonus') return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Battle] Не удалось определить тип Вашего баланса!'
        ];
		if(is_null($color) || $color != 'red' && $color != 'blue') return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Battle] Цвет не найден'
        ];
		if(is_null($sum)) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Battle] Вы ввели неверную сумму'
        ];
		if($sum < $this->settings->battle_min_bet) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Battle] Минимальная сумма ставки '.$this->settings->battle_min_bet.' руб!'
        ];
		if($sum > $this->settings->battle_max_bet) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Battle] Максимальная сумма ставки '.$this->settings->battle_max_bet.' руб!'
        ];
		if($this->game->status == Battle::STATUS_PRE_FINISH || $this->game->status == Battle::STATUS_FINISHED) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Battle] Игра уже началась или закончилась!'
        ];
		
		$bets = BattleBets::where([
            'user_id' => $user->id,
            'game_id' => $this->game->id
        ])->select('color', 'balType')->groupBy('color', 'balType')->get();
		
		$countbets = BattleBets::where('game_id', $this->game->id)->where('user_id', $user->id)->count();
		if($countbets >= 3) return [
            'success' => false,
            'fake' => $this->settings->fakebets,
            'msg' => '[Battle] Разрешено только 3 ставки!'
        ];
		
		foreach($bets as $b) {
			if($balType != $b->balType) return [
				'success' => false,
				'fake' => $this->settings->fakebets,
				'msg' => '[Battle] Вы уже сделали ставку с '. (($balType == 'balance') ? 'бонусного' : 'реального') .' счета!'
			];
			if($color != $b->color) return [
				'success' => false,
				'fake' => $this->settings->fakebets,
				'msg' => '[Battle] Вы уже сделали ставку на другой цвет!'
			];
		}
		
		DB::beginTransaction();
		try {
			$bet = new BattleBets();
			$bet->user()->associate($user);
			$bet->price = $sum;
			$bet->color = $color;
			$bet->game()->associate($this->game);
			$bet->balType = $balType;
			$bet->fake = 1;
			$bet->save();

			$bets = BattleBets::where('game_id', $this->game->id);
			$this->game->price = $bets->sum('price');
			
			DB::commit();
		} catch (\PDOException $e) {
			DB::connection()->getPdo()->rollBack();
			return [
				'success' => false,
				'fake' => $this->settings->fakebets,
				'msg' => '[Battle] Ошибка сервера!'
			];
		}
		
		$this->game->save();
		$bets = BattleBets::where('game_id', $this->game->id)->get();

		$this->redis->publish('battle.newBet', json_encode([
			'bank' 		=> [round($this->getBankForGame($this->game, 'red'), 2), round($this->getBankForGame($this->game, 'blue'), 2)],
			'bets' 		=> $this->getBets(),
			'tickets' 	=> $this->getTicketsOfGame($this->game),
			'factor' 	=> [$this->getXForGame($this->game, 'red'), $this->getXForGame($this->game, 'blue')],
			'chances' 	=> [floor($this->getChanceOfColor('red', $this->game)), round($this->getChanceOfColor('blue', $this->game))],
			'count' 	=> [$this->getCountOfColor('red', $this->game), $this->getCountOfColor('blue', $this->game)]
		]));
		
		if($this->getUserInGame()[0] >= 1 && $this->getUserInGame()[1] >= 1) {
			if($this->game->status < Battle::STATUS_PLAYING) {
				$this->game->status = Battle::STATUS_PLAYING;
				$this->game->save();
				$this->startTimer();
			}
        }
		
		return [
			'success' => true,
			'fake' => $this->settings->fakebets,
			'msg' => '[Battle] Ваша ставка принята!'
		];
	}
	
	public function getSlider() {
		$winTicket = mt_rand(1, 1000);
		if($this->game->winner_team == 'red') {
			$winTicket = mt_rand(1, $this->getTicketsOfGame($this->game)[0]);
		}
		if($this->game->winner_team == 'blue') {
			$winTicket = mt_rand($this->getTicketsOfGame($this->game)[1], 1000);
		}
		$red = $this->getChanceOfColor('red', $this->game) * 10;
		$winner = 'red';
		if($winTicket > $red) $winner = 'blue';
		
        $this->game->status         = Battle::STATUS_FINISHED;
		$this->game->winner_team 	= $winner;
		$this->game->winner_factor 	= $this->getXForGame($this->game, $winner);
		$this->game->winner_ticket 	= $winTicket;
		$this->game->commission 	= $this->sendWinMoney($this->game);
        $this->game->save();

        $returnValue = [
			'game' => $this->game,
            'ticket' => $winTicket,
        ];

        return response()->json($returnValue);
	}
	
	public function sendWinMoney($game) {
		$color = 'blue';
		$comission = 0;
		if($game->winner_team == 'red') $color = 'red'; 
		$bets = BattleBets::where('game_id', $game->id)->where('color', $color)->get();
		
		foreach($bets as $bet) {
			$user = User::where(['id' => $bet->user_id, 'fake' => 0])->first();
			$winmoney = $bet->price * $this->getXForGame($game, $color);
			$comission += round(($winmoney - $bet->price)/100 * $this->settings->battle_commission, 2);
			$sum = $bet->price + round(($winmoney - $bet->price) - ($winmoney - $bet->price)/100*$this->settings->battle_commission, 2);
			
			if(!is_null($user)) {
				if($bet->balType == 'balance') {
					$user->balance += $sum;
					$user->requery += round((($winmoney - $bet->price) - ($winmoney - $bet->price)/100*$this->settings->battle_commission)/100*$this->settings->requery_perc, 3);
					$user->save();

					if($user->ref_id) {
						$ref = User::where('unique_id', $user->ref_id)->first();
						if($ref) {
							$ref_sum = round((($winmoney - $bet->price) - ($winmoney - $bet->price)/100*$this->settings->battle_commission)/100*$this->settings->ref_perc, 2);
							if($ref_sum) {
								$ref->ref_money += $ref_sum;
								$ref->ref_money_all += $ref_sum;
								$ref->save();

								Profit::create([
									'game' => 'ref',
									'sum' => -$ref_sum
								]);
							}
						}
					}

					if($comission > 0) Profit::create([
						'game' => 'battle',
						'sum' => $comission
					]);

					$this->redis->publish('updateBalanceAfter', json_encode([
						'unique_id' => $user->unique_id,
						'balance'	=> round($user->balance, 2),
						'timer'		=> 6
					]));
				}

				if($bet->balType == 'bonus') {
					$user->bonus += $sum;
					$user->save();

					$this->redis->publish('updateBonusAfter', json_encode([
						'unique_id' => $user->unique_id,
						'bonus' 	=> round($user->bonus, 2),
						'timer'		=> 6
					]));
				}
			}
			
			$bet->win = 1;
			$bet->win_sum = $sum;
			$bet->save();
		}
		
		return $comission;
	}
	
	private function findUser($id) {
        $user = User::where('id', $id)->first();
        return $user;
    }
	
	public function getXForGame($game, $color) {
		$betsum = BattleBets::where('game_id', $game->id)->where('balType', 'balance')->where('color', $color)->sum('price');
		$betsum_bonus = BattleBets::where('game_id', $game->id)->where('balType', 'bonus')->where('color', $color)->sum('price');
		$betsum = $betsum + $betsum_bonus/$this->settings->exchange_curs;
		if($betsum >= 0.01) {
			$x = round($this->getBank($game) / $betsum, 2);
			return (!$this->getBank($game)) ? 2 : $x;
		}
		return 2;
	}
	
	public function getBankForGame($game, $color) {
		$bets = BattleBets::where('game_id', $game->id)->where('balType', 'balance')->where('color', $color)->sum('price');
		$bets_bonus = BattleBets::where('game_id', $game->id)->where('balType', 'bonus')->where('color', $color)->sum('price');
		$bets = $bets + $bets_bonus/$this->settings->exchange_curs;
		return round($bets, 2);
	}
	
	public function getBank($game) {
		$bets = BattleBets::where('game_id', $game->id)->where('balType', 'balance')->sum('price');
		$bets_bonus = BattleBets::where('game_id', $game->id)->where('balType', 'bonus')->sum('price');
		$bets = $bets + $bets_bonus/$this->settings->exchange_curs;
		return $bets;
	}
	
	public function getChanceOfColor($color, $game) {
        $chance = 0;
        if(!is_null($color)) {
            $bet = BattleBets::where('game_id', $game->id)
                ->where('color', $color)
                ->where('balType', 'balance')
                ->sum('price');
            $bet_bonus = BattleBets::where('game_id', $game->id)
                ->where('color', $color)
                ->where('balType', 'bonus')
                ->sum('price');
			
			$betSum = $bet + $bet_bonus/$this->settings->exchange_curs;
			
			if($bet || $bet_bonus) $chance = round($betSum / $this->getBank($game), 2) * 100;
        }
		$chance = ($this->getBank($game) == 0) ? 50 : $chance;
        return $chance;
    }
	
	public function getCountOfColor($color, $game) {
        $count = 0;
        if(!is_null($color)) {
			$users = [];

			$bets = BattleBets::where('game_id', $game->id)->orderBy('id', 'desc')->get();
			foreach($bets->where('color', $color) as $bet) {
				$find = 0;
				foreach($users as $user) if($user == $bet->user_id) $find++;
				if($find == 0) $users[] = $bet->user_id;
			}
        }
        return count($users);
    }
	
	public function getTicketsOfGame($game) {
		$red = $this->getChanceOfColor('red', $game) * 10;
		if($red < 0.01) $red = 1;
		$blue = $this->getChanceOfColor('blue', $game) * 10;
		if($red >= 0.01) $blue = ($red == 1000) ? ($red - 1) : $red;
		return [round($red), round($blue + 1, 2)];
	}
	
	public function getStatus(Request $r) {
		$game = Battle::orderBy('id', 'desc')->first();
		
        return response()->json([
            'id'      	=> $game->id,
            'time'      => $this->settings->battle_timer,
            'status'    => $game->status
        ]);
    }
	
	public function getUserInGame() {
		$game = $this->getLastGame();
        if(!$game) return;
		
		$users_red = [];
        $users_blue = [];
		
        $bets = BattleBets::where('game_id', $game->id)->orderBy('id', 'desc')->get();
        foreach($bets->where('color', 'red') as $bet) {
            $find = 0;
            foreach($users_red as $user) if($user == $bet->user_id) $find++;
            if($find == 0) $users_red[] = $bet->user_id;
        }
        foreach($bets->where('color', 'blue') as $bet) {
            $find = 0;
            foreach($users_blue as $user) if($user == $bet->user_id) $find++;
            if($find == 0) $users_blue[] = $bet->user_id;
        }
        
        return [count($users_red), count($users_blue)];
    }
	
	public function setStatus(Request $r) {
        $this->game->status = $r->get('status');
        $this->game->save();
        return $this->game->status;
    }
	
	private function startTimer() {
        $this->redis->publish('battle.startTime', json_encode([
            'time' => $this->settings->battle_timer
        ]));
    }
	
	public function getTime() {
        $min = floor($this->settings->battle_timer/60);
        $sec = floor($this->settings->battle_timer-($min*60));
		
        if($min == 0) $min = '00';
        if($sec == 0) $sec = '00';
        if(($min > 0) && ($min < 10)) $min = '0'.$min;
        if(($sec > 0) && ($sec < 10)) $sec = '0'.$sec;
        return [$min, $sec, $this->settings->battle_timer];
    }
	
	public function gotThis(Request $r) {
		$color = $r->get('color');
		
		if($this->game->status > 1) return [
			'msg'       => 'Игра началась, вы не можете подкрутить!',
			'type'      => 'error'
		];
        
		if(!$this->game->id) return [
			'msg'       => 'Не удалось получить номер игры!',
			'type'      => 'error'
		];
		
		if(!$color) return [
			'msg'       => 'Не удалось получить цвет!',
			'type'      => 'error'
		];

		Battle::where('id', $this->game->id)->update([
			'winner_team'      => $color
		]);
		
		if($color == 'red') $color = 'красной';
		if($color == 'blue') $color = 'синей';
		
		return [
			'msg'       => 'Вы подкрутили '.$color.' коменде!',
			'type'      => 'success'
		];
	}
	
	private function getUser() {
        $user = User::where('fake', 1)->inRandomOrder()->first();
		if($user->time != 0) {
			$now = Carbon::now()->format('H');
			if($now < 06) $time = 4;
			if($now >= 06 && $now < 12) $time = 1;
			if($now >= 12 && $now < 18) $time = 2;
			if($now >= 18) $time = 3;
        	$user = User::where(['fake' => 1, 'time' => $time])->inRandomOrder()->first();
		}
        return $user;
    }
}