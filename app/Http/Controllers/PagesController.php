<?php namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Jackpot;
use App\JackpotBets;
use App\Wheel;
use App\WheelBets;
use App\Crash;
use App\CrashBets;
use App\CoinFlip;
use App\Battle;
use App\BattleBets;
use App\Hilo;
use App\HiloBets;
use App\Sends;
use App\Dice;
use App\Bonus;
use App\BonusLog;
use App\Payments;
use App\Exchanges;
use App\Withdraw;
use App\Profit;
use App\Promocode;
use App\PromoLog;
use App\Giveaway;
use App\GiveawayUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use DB;

class PagesController extends Controller
{
    public function __construct()
    {
        parent::__construct();
		DB::connection()->getPdo()->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    }
	
	public function faq() {
		return view('pages.faq');
	}
	
	public function index() {
		return view('pages.index');
	}
	
	public function profileHistory() {
		$pays = Payments::where(['user_id' => $this->user->id, 'status' => 1])->orderBy('id', 'desc')->get();
        $withdraws = Withdraw::where('user_id', $this->user->id)->orderBy('id', 'desc')->get();
		return view('pages.profileHistory', compact('pays', 'withdraws'));
	}
	
	public function free() {
		$rotate = 0;
		$bonuses = Bonus::get();
		foreach($bonuses as $key => $b) {
			$bonuses[$key]['rotate'] = $rotate;
			$rotate += 360 / $bonuses->count();
		}
		$max = Bonus::where('type', 'group')->max('sum');
		$max_refs = Bonus::where('type', 'refs')->max('sum');
		
		$bonusLog = BonusLog::where(['user_id' => $this->user->id, 'type' => 'group'])->orderBy('id', 'desc')->first();
		$check = 0;
		if($bonusLog) {
			if($bonusLog->remaining) {
				$nowtime = time();
				$time = $bonusLog->remaining;
				$lasttime = $nowtime - $time;
				if($time >= $nowtime) {
					$check = 1;
				}
			}
			$bonusLog->status = 2;
			$bonusLog->save();
		}
		
		$activeRefs = 0;
		$refs = User::where(['ban' => 0, 'ref_id' => $this->user->unique_id])->get();
		foreach($refs as $a) {
			$pay = Payments::where(['user_id' => $a->id, 'status' => 1])->sum('sum');
			if($pay >= 100) $activeRefs += 1;
		}
		
		$refLog = BonusLog::where(['user_id' => $this->user->id, 'type' => 'refs', 'status' => 3])->count();
		
		return view('pages.free', compact('bonuses', 'max', 'max_refs', 'check', 'activeRefs', 'refLog'));
	}
	
	public function freeGetWheel(Request $r) {
		$type = $r->get('type');
		$bonuses = Bonus::select('bg', 'sum', 'color')->where('type', $type)->get();
		$list = [];
		foreach($bonuses as $b) {
			$list[] = [
				'sum' => $b->sum,
				'bgColor' => $b->bg,
				'iconColor' => $b->color
			];
		}
		$bonusLog = BonusLog::where('user_id', $this->user->id)->where('type', $type)->orderBy('id', 'desc')->first();
		$remaining = isset($bonusLog) ? $bonusLog->remaining : 0;
		$data = [
			'data' => $list,
			'remaining' => $remaining,
			'type' => $type
		];
		return $data;
	}
	
	public function freeSpin(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		$validator = \Validator::make($r->all(), [
            'recapcha' => 'required|captcha',
        ]);
		if($validator->fails()) return response()->json(['success' => false, 'msg' => 'Вы не прошли проверку на я не робот!', 'type' => 'error']);
		$type = $r->get('type');
		if($type == 'group') {
			$vk_ckeck = $this->groupIsMember($this->user->user_id);

			if($vk_ckeck == 0) return response()->json(['success' => false, 'msg' => 'Вы не состоите в нашей группе!', 'type' => 'error']);
			if($vk_ckeck == NULL) return response()->json(['success' => false, 'msg' => 'Выдача бонусов временно не работает!', 'type' => 'error']);

			$bonuses = Bonus::select('bg', 'sum', 'color', 'status')->where('type', $type)->get();

			$bonusLog = BonusLog::where('user_id', $this->user->id)->where('type', $type)->orderBy('id', 'desc')->first();
			if($bonusLog) {
				if($bonusLog->remaining) {
					$nowtime = time();
					$time = $bonusLog->remaining;
					$lasttime = $nowtime - $time;
					if($time >= $nowtime) {
						return [
							'success' => false,
							'msg' => 'Следующий бонус Вы сможете получить: '.date("d.m.Y H:i:s", $time),
							'type' => 'error'
						];
					}
				}
				$bonusLog->status = 2;
				$bonusLog->save();
			}

			$start = (360/$bonuses->count())/2;
			foreach($bonuses as $key => $b) {
				$bonuses[$key]['start'] = $start;
				$start += 360/$bonuses->count();
			}

			$list = [];
			foreach($bonuses as $b) {
				if($b->status == 1) $list[] = [
					'sum' => $b->sum,
					'start' => $b->start
				];
			}
			$win = $list[array_rand($list)];

			$remaining = Carbon::now()->addMinutes($this->settings->bonus_group_time)->getTimestamp();

			BonusLog::create([
				'user_id' => $this->user->id,
				'sum' => $win['sum'],
				'remaining' => $remaining,
				'status' => 1,
				'type' => $type
			]);

			$this->user->balance += $win['sum'];
			$this->user->save();

			$this->redis->publish('updatebalanceAfter', json_encode([
				'unique_id'	=> $this->user->unique_id,
				'balance' 	=> round($this->user->balance, 2),
				'timer' 	=> 5
			]));
		}
		
		if($type == 'refs') {
			$bonuses = Bonus::select('bg', 'sum', 'color', 'status')->where('type', $type)->get();
		
			$activeRefs = 0;
			$refs = User::where(['ban' => 0, 'ref_id' => $this->user->unique_id])->get();
			foreach($refs as $a) {
				$pay = Payments::where(['user_id' => $a->id, 'status' => 1])->sum('sum');
				if($pay >= 100) $activeRefs += 1;
			}
			
			if($activeRefs < $this->settings->max_active_ref) return response()->json(['success' => false, 'msg' => 'Недостаточно активных рефералов. '.$activeRefs.'/'.$this->settings->max_active_ref.'!', 'type' => 'error']);

			$bonusLog = BonusLog::where('user_id', $this->user->id)->where('type', $type)->orderBy('id', 'desc')->first();
			if($bonusLog) {
				if($bonusLog->status == 3) return response()->json(['success' => false, 'msg' => 'Вы уже получили этот бонус!', 'type' => 'error']);
			}

			$start = (360/$bonuses->count())/2;
			foreach($bonuses as $key => $b) {
				$bonuses[$key]['start'] = $start;
				$start += 360/$bonuses->count();
			}

			$list = [];
			foreach($bonuses as $b) {
				if($b->status == 1) $list[] = [
					'sum' => $b->sum,
					'start' => $b->start
				];
			}
			$win = $list[array_rand($list)];

			$remaining = 0;
			
			BonusLog::create([
				'user_id' => $this->user->id,
				'sum' => $win['sum'],
				'remaining' => $remaining,
				'status' => 3,
				'type' => $type
			]);

			$this->user->bonus += $win['sum'];
			$this->user->save();

			$this->redis->publish('updateBonusAfter', json_encode([
				'unique_id'	=> $this->user->unique_id,
				'bonus' 	=> round($this->user->bonus, 2),
				'timer' 	=> 5
			]));
		}
		
		$this->redis->publish('bonus', json_encode([
			'unique_id' => $this->user->unique_id,
            'rotate' => 1440+$win['start']
        ]));
		
		return response()->json(['success' => true, 'msg' => 'Крутим!', 'type' => 'success', 'remaining' => $remaining, 'bonusType' => $type]);
	}
	
		public function paySend() {
		return view('pages.paySend');
	}
	
    public function sendCreate(Request $r) {
		if(\Cache::has('bet.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('bet.user.' . $this->user->id, '', 0.10);
        $target = $r->get('target');
        $sum = $r->get('sum');
        $value = floor($sum*1.05);
        
        $with = Withdraw::where('user_id', $this->user->id)->where('status', 1)->sum('value');
        $user = User::where('user_id', $target)->first();
        
		if($value < 1) {
			return [
				'success' => false,
				'msg' => 'Вы ввели не правильное значение!',
				'type' => 'error'
			];
		}
        
		if(!$this->user->is_admin && !$this->user->is_youtuber) {
			if($with < 250) return [
				'success' => false,
				'msg' => 'Вы не сделали вывод в размере 250 рублей!',
				'type' => 'error'
			];
		}
        
        if(!$user) return [
            'success' => false,
            'msg' => 'Пользователя с таким ID нет!',
            'type' => 'error'
        ];
        
        if($target == $this->user->user_id) return [
            'success' => false,
            'msg' => 'Вы не можете отправлять монеты себе!',
            'type' => 'error'
        ];
        
        if($value > $this->user->balance) return [
            'success' => false,
            'msg' => 'Вы не можете отправить сумму больше чем Ваш баланс!',
            'type' => 'error'
        ];
        
        if($value < 20) return [
            'success' => false,
            'msg' => 'Минимальная сумма перевода 20 монет!',
            'type' => 'error'
        ];
        
        if(!$value || !$target) return [
            'success' => false,
            'msg' => 'Вы не вели одно из значений!',
            'type' => 'error'
        ];
        
        $this->user->balance -= $value;
        $this->user->save();
        
        $user->balance += $sum;
        $user->save();
		
		Sends::create([
			'sender' => $this->user->id,
			'receiver' => $user->id,
			'sum' => $sum
		]);
        
        $this->redis->publish('updateBalance', json_encode([
            'id'      => $this->user->id,
            'balance' => $this->user->balance
        ]));
        
        $this->redis->publish('updateBalance', json_encode([
            'id'      => $user->id,
            'balance' => $user->balance
        ]));
        
        return [
            'success' => true,
            'msg' => 'Вы перевели '.$sum.' <i class="fas fa-coins"></i> пользователю '.$user->username.'!',
            'type' => 'success'
        ];        
    }
	
	public function payHistory() {
		$pays = SuccessPay::where('user', $this->user->user_id)->where('status', '>=', 1)->get();
        $withdraws = Withdraw::where('user_id', $this->user->id)->where('status', '>', 0)->get();
		$active = Withdraw::where('user_id', $this->user->id)->where('status', 0)->get();
		return view('pages.payHistory', compact('pays', 'withdraws', 'active'));
	}
	
	public function promoActivate(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['success' => false, 'msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		
		$code = strtolower(htmlspecialchars($r->get('code')));
        if(!$code) return response()->json(['success' => false, 'msg' => 'Вы не ввели код!', 'type' => 'error']);
		
        $promocode = Promocode::where('code', $code)->first();
        if(!$promocode) return response()->json(['success' => false, 'msg' => 'Такого кода не существует!', 'type' => 'error']);
		
		$money = $promocode->amount;
		$check = PromoLog::where('user_id', $this->user->id)->where('code', $code)->first();

		if($check) return response()->json(['success' => false, 'msg' => 'Вы уже активировали код!', 'type' => 'error']);
		if($promocode->limit == 1 && $promocode->count_use <= 0) return response()->json(['success' => false, 'msg' => 'Код больше не действителен!', 'type' => 'error']);
		if($promocode->user_id == $this->user->id) return response()->json(['success' => false, 'msg' => 'Вы не можете активировать свой промокод!', 'type' => 'error']);

		if($promocode->type == 'balance') {
			$this->user->balance += $money;
			$this->user->save();
			
			Profit::create([
				'game' => 'ref',
				'sum' => -$money
			]);
			
			$this->redis->publish('updateBalance', json_encode([
				'unique_id' => $this->user->unique_id,
				'balance'	=> round($this->user->balance, 2)
			]));
		}

		if($promocode->type == 'bonus') {
			$this->user->bonus += $money;
			$this->user->save();
			
			$this->redis->publish('updateBonus', json_encode([
				'unique_id' => $this->user->unique_id,
				'bonus'	=> round($this->user->bonus, 2)
			]));
		}

		if($promocode->limit == 1 && $promocode->count_use > 0){
			$promocode->count_use -= 1;
			$promocode->save();
		}

		PromoLog::insert([
			'user_id' => $this->user->id,
			'sum' => $money,
			'code' => $code,
			'type' => $promocode->type
		]);
		
		return response()->json(['success' => true, 'msg' => 'Код активирован!', 'type' => 'success']);
	}
	
	public function affiliate() {
		return view('pages.affiliate');
	}
	
	public function affiliateGet() {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		if($this->user->ref_money < $this->settings->min_ref_withdraw) return response()->json(['success' => false, 'msg' => 'Минимальная сумма снятия '. $this->settings->min_ref_withdraw .'  монет!', 'type' => 'error']);
		
		DB::beginTransaction();

        try {
			$this->user->bonus += $this->user->ref_money;
			$this->user->ref_money = 0;
			$this->user->save();
			
			DB::commit();
		} catch(Exception $e) {
            DB::rollback();
			return ['msg' => 'Что-то пошло не так...', 'type' => 'error'];
        }
		
		$this->redis->publish('updateBonus', json_encode([
			'unique_id' => $this->user->unique_id,
			'bonus' 	=> round($this->user->bonus, 2)
		]));
		
		return response()->json(['success' => true, 'msg' => 'Монеты переведены на Ваш бонусный счет!', 'type' => 'success']);
	}
	
	public function exchange(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		$sum = floatval($r->get('sum'));
		
		if($sum < $this->settings->exchange_min) return ['msg' => 'Минимальная сумма обмена '.$this->settings->exchange_min.' монет!', 'type' => 'error'];
		if($this->user->bonus < $sum) return ['msg' => 'На бонусном балансе недостаточно средств!', 'type' => 'error'];
		
		DB::beginTransaction();
		try {

			$exchange = new Exchanges();
			$exchange->user_id = $this->user->id;
			$exchange->sum = $sum;
			$exchange->save();
			
			$curs = round($sum/$this->settings->exchange_curs, 2);
			$this->user->bonus -= $sum;
			$this->user->balance += $curs;
			$this->user->save();
			
			Profit::create([
				'game' => 'exchange',
				'sum' => $sum-$curs
			]);
			
			$this->redis->publish('updateBalance', json_encode([
				'unique_id' => $this->user->unique_id,
				'balance'	=> round($this->user->balance, 2)
			]));
			
			$this->redis->publish('updateBonus', json_encode([
				'unique_id' => $this->user->unique_id,
				'bonus'		=> round($this->user->bonus, 2)
			]));

			DB::commit();
		} catch (\PDOException $e){
			DB::rollback();
			return ['msg' => 'Что-то пошло не так...', 'type' => 'error'];
		}
		
		return ['msg' => 'Вы обменяли '.$sum.' бонусов на '.$curs.' монет!', 'type' => 'success'];
	}
	
	public function joinGiveaway(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		
		$giveaway = Giveaway::where('id', $r->get('id'))->first();
		
		if(is_null($giveaway)) return ['msg' => 'Не удалось найти раздачу!', 'type' => 'error'];
		if($giveaway->status > 0) return ['msg' => 'Раздача уже закончилась!', 'type' => 'error'];
		
		$check = GiveawayUsers::where(['giveaway_id' => $giveaway->id, 'user_id' => $this->user->id])->count();
		if($check > 0) return ['msg' => 'Вы уже участвуете в этой раздаче!', 'type' => 'error'];
		
		if($giveaway->group_sub != 0) {
			$vk_ckeck = $this->groupIsMember($this->user->user_id);

			if($vk_ckeck == 0) return response()->json(['msg' => 'Вы не состоите в нашей группе!', 'type' => 'error']);
			if($vk_ckeck == NULL) return response()->json(['msg' => 'Выдача бонусов временно не работает!', 'type' => 'error']);
		}
		
		if($giveaway->min_dep != 0) {
			$dep = Payments::where('user_id', $this->user->id)->where('updated_at', '>=', Carbon::today())->where('status', 1)->sum('sum');

			if($dep < $giveaway->min_dep) {
                return response()->json(['msg' => 'Для того чтобы участвовать в раздаче, Вам нужно пополнить счет на сумму '.$giveaway->min_dep.' р. за текущий день.!', 'type' => 'error']);
            }
		}
		
		DB::beginTransaction();
		try {

			$gv_user = new GiveawayUsers();
			$gv_user->giveaway_id = $giveaway->id;
			$gv_user->user_id = $this->user->id;
			$gv_user->save();
			
			$users = GiveawayUsers::where('giveaway_id', $giveaway->id)->count();
			
			$array = [
				'type' => 'newUser',
				'id' => $giveaway->id,
				'count' => $users
			];
			
			$this->redis->publish('giveaway', json_encode($array));

			DB::commit();
		} catch (\PDOException $e){
			DB::rollback();
			return ['msg' => 'Что-то пошло не так...', 'type' => 'error'];
		}
		
		return ['msg' => 'Вы вступили в разначу #'.$giveaway->id.'!', 'type' => 'success'];
	}
	
	public function getGiveaway() {
		$giveaway = Giveaway::orderBy('id', 'desc')->where('status', 0)->get();
		return $giveaway;
	}
	
	public function endGiveaway(Request $r) {
		$id = $r->get('id');
		$gv = Giveaway::where('id', $id)->first();
		if(is_null($gv)) return ['msg' => 'Не удалось найти раздачу стаким ID!', 'type' => 'false'];
		if($gv->status > 0) return ['msg' => 'Эта раздача уже закончилась!', 'type' => 'false'];
		$count = GiveawayUsers::where('giveaway_id', $gv->id)->count();
		
		$winner = null;
		if($count >= 1) {
		
			if(!is_null($gv->winner_id)) {
				$winner_id = $gv->winner_id;
			} else {
				$gvu = GiveawayUsers::where('giveaway_id', $gv->id)->inRandomOrder()->first();
				$winner_id = $gvu->user_id;
			}
			
			$winner = User::getUser($winner_id);
			$w = User::where('id', $winner_id)->first();
			$gv->winner_id = $w->id;
			$gv->save();

			if(!$w->fake) {
				$w[$gv->type] += $gv->sum;
				$w->save();
				if($gv->type == 'balance') {
					Profit::create([
						'game' => 'ref',
						'sum' => -$gv->sum
					]);
					$this->redis->publish('updateBalance', json_encode([
						'unique_id' => $w->unique_id,
						'balance' 	=> round($w->balance, 2)
					]));
				}
				if($gv->type == 'bonus') {
					$this->redis->publish('updateBonus', json_encode([
						'unique_id' => $w->unique_id,
						'bonus' 	=> round($w->bonus, 2)
					]));
				}
			}
		}
		
		$gv->status = 1;
		$gv->save();
		
		$array = [
			'type' => 'winner',
			'id' => $gv->id,
			'winner' => $winner
		];

		$this->redis->publish('giveaway', json_encode($array));
		
		return ['msg' => 'Победитель выбран в раздаче #'.$gv->id.'!', 'type' => 'success'];
	}
	
	public function fairCheck(Request $r) {
		$hash = $r->get('hash');
		if(!$hash) return [
			'success' => false,
			'type' => 'error',
			'msg' => 'Поле не может быть пустым!'
		];
		$jackpot = Jackpot::where(['hash' => $hash, 'status' => 3])->first();
		$wheel = Wheel::where(['hash' => $hash, 'status' => 3])->first();
		$crash = Crash::where(['hash' => $hash, 'status' => 2])->first();
		$coin = CoinFlip::where(['hash' => $hash, 'status' => 1])->first();
		$battle = Battle::where(['hash' => $hash, 'status' => 3])->first();
		$hilo = Hilo::where(['hash' => $hash, 'status' => 4])->first();
		$dice = Dice::where('hash', $hash)->first();
		
		if(!is_null($jackpot)) {
			$info = [
				'id' => $jackpot->game_id,
				'number' => $jackpot->winner_ticket
			];
		} elseif(!is_null($wheel)) {
			$info = [
				'id' => $wheel->id,
				'number' => ($wheel->winner_color == 'black' ? 2 : ($wheel->winner_color == 'red' ? 3 : ($wheel->winner_color == 'green' ? 5 : 50)))
			];
		} elseif(!is_null($crash)) {
			$info = [
				'id' => $crash->id,
				'number' => $crash->multiplier
			];
		} elseif(!is_null($coin)) {
			$info = [
				'id' => $coin->id,
				'number' => $coin->winner_ticket
			];
		} elseif(!is_null($battle)) {
			$info = [
				'id' => $battle->id,
				'number' => $battle->winner_ticket
			];
		} elseif(!is_null($hilo)) {
			$info = [
				'id' => $hilo->id,
				'number' => ($hilo->card_section == 'joker' ? 'joker' : $hilo->card_name)
			];
		} elseif(!is_null($dice)) {
			$info = [
				'id' => $dice->id,
				'number' => $dice->num
			];
		} else {
			return [
				'success' => false,
				'type' => 'error',
				'msg' => 'Неверный хэш или раунд еще не сыгран!'
			];
		}
		
		return [
			'success' => true,
			'type' => 'success',
			'msg' => 'Хэш найден!',
			'round' => $info['id'],
			'number' => $info['number']
		];
	}
	
	public function unbanMe() {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		if(!$this->user->banchat) return [
			'success' => false,
			'type' => 'error',
			'msg' => 'Вы не забанены в чате!'
		];
		if($this->user->balance < 50) return [
			'success' => false,
			'type' => 'error',
			'msg' => 'У Вас недостаточно средств для разблокировки!'
		];
		
		$this->user->balance -= 50;
		$this->user->banchat = null;
		$this->user->save();
		
		$returnValue = ['unique_id' => $this->user->unique_id, 'ban' => 0];
		$this->redis->publish('ban.msg', json_encode($returnValue));
		
		$this->redis->publish('updateBalance', json_encode([
            'unique_id' => $this->user->unique_id,
            'balance' 	=> $this->user->balance
        ]));
		
		return [
			'success' => false,
			'type' => 'success',
			'msg' => 'Вы разблокированы в чате!'
		];
	}
	
	public function getUser(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		if(is_null($r->get('id'))) return response()->json(['success' => false, 'msg' => 'Не удалось найти пользователя!', 'type' => 'error']);
		$user = User::where('unique_id', $r->get('id'))->select('username', 'avatar', 'unique_id', 'id')->first();
		if(is_null($user)) return response()->json(['success' => false, 'msg' => 'Не удалось найти пользователя!', 'type' => 'error']);
		
		$jackpotSum = JackpotBets::join('jackpot', 'jackpot.id', '=', 'jackpot_bets.game_id')
			->select('jackpot.status', 'jackpot_bets.sum')
			->where('jackpot.status', 3)
			->where(['jackpot_bets.user_id' => $user->id])
			->sum('jackpot_bets.sum');
		$wheelSum = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel_bets.price')
			->where('wheel.status', 3)
			->where(['wheel_bets.user_id' => $user->id])
			->sum('wheel_bets.price');
		$crashSum = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash_bets.price')
			->where('crash.status', 2)
			->where(['crash_bets.user_id' => $user->id])
			->sum('price');
		$coinSum = CoinFlip::where('heads', $user->id)->orWhere('tails', $user->id)->where('status', 1)->sum('bank')/2;
		$battleSum = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle_bets.price')
			->where('battle.status', 3)
			->where(['battle_bets.user_id' => $user->id])
			->sum('battle_bets.price');
		$diceSum = Dice::where('user_id', $user->id)->sum('sum');
		$hiloSum = HiloBets::join('hilo', 'hilo.id', '=', 'hilo_bets.game_id')
			->select('hilo.status', 'hilo_bets.sum')
			->where('hilo.status', 4)
			->where(['hilo_bets.user_id' => $user->id])
			->sum('hilo_bets.sum');
		$betAmount = $jackpotSum+$wheelSum+$crashSum+$coinSum+$battleSum+$diceSum+$hiloSum;
		
		$jackpotCount = JackpotBets::join('jackpot', 'jackpot.id', '=', 'jackpot_bets.game_id')
			->select('jackpot.status', 'jackpot.id', 'jackpot_bets.game_id')
			->where('jackpot.status', 3)
			->where(['jackpot_bets.user_id' => $user->id])
			->groupBy('jackpot_bets.game_id')
			->get()->count();
		$wheelCount = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel.id', 'wheel_bets.game_id')
			->where('wheel.status', 3)
			->where(['wheel_bets.user_id' => $user->id])
			->groupBy('wheel_bets.game_id')
			->get()->count();
		$crashCount = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash.id', 'crash_bets.round_id')
			->where('crash.status', 2)
			->where(['crash_bets.user_id' => $user->id])
			->groupBy('crash_bets.round_id')
			->get()->count();
		$coinCount1 = CoinFlip::where('heads', $user->id)->where('status', 1)->count();
		$coinCount2 = CoinFlip::where('tails', $user->id)->where('status', 1)->count();
		$coinCount = $coinCount1+$coinCount2;
		$battleCount = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle.id', 'battle_bets.game_id')
			->where('battle.status', 3)
			->where(['battle_bets.user_id' => $user->id])
			->groupBy('battle_bets.game_id')
			->get()->count();
		$diceCount = Dice::where('user_id', $user->id)->count();
		$wheelCount = HiloBets::join('hilo', 'hilo.id', '=', 'hilo_bets.game_id')
			->select('hilo.status', 'hilo.id', 'hilo_bets.game_id')
			->where('hilo.status', 4)
			->where(['hilo_bets.user_id' => $user->id])
			->groupBy('hilo_bets.game_id')
			->get()->count();
		$betCount = $jackpotCount+$wheelCount+$crashCount+$coinCount+$battleCount+$diceCount+$wheelCount;
		
		$jackpotWin = Jackpot::where(['winner_id' => $user->id])->where('status', 3)->count();
		$wheelWin = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel.id', 'wheel_bets.game_id')
			->where('wheel.status', 3)
			->where(['wheel_bets.user_id' => $user->id, 'wheel_bets.win' => 1])
			->groupBy('wheel_bets.game_id')
			->get()->count();
		$crashWin = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash.id', 'crash_bets.round_id')
			->where('crash.status', 2)
			->where(['crash_bets.user_id' => $user->id, 'crash_bets.status' => 1])
			->groupBy('crash_bets.round_id')
			->get()->count();
		$coinWin = CoinFlip::where('winner_id', $user->id)->count();
		$battleWin = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle.id', 'battle_bets.game_id')
			->where('battle.status', 3)
			->where(['battle_bets.user_id' => $user->id, 'battle_bets.win' => 1])
			->groupBy('battle_bets.game_id')
			->get()->count();
		$diceWin = Dice::where(['user_id' => $user->id, 'win' => 1])->count();
		$hiloWin = HiloBets::join('hilo', 'hilo.id', '=', 'hilo_bets.game_id')
			->select('hilo.status', 'hilo.id', 'hilo_bets.game_id')
			->where('hilo.status', 4)
			->where(['hilo_bets.user_id' => $user->id, 'hilo_bets.win' => 1])
			->groupBy('hilo_bets.game_id')
			->get()->count();
		$betWin = $jackpotWin+$wheelWin+$crashWin+$coinWin+$battleWin+$diceWin+$hiloWin;
		
		$jackpotLose = JackpotBets::join('jackpot', 'jackpot.id', '=', 'jackpot_bets.game_id')
			->select('jackpot.status', 'jackpot.id', 'jackpot_bets.game_id', 'jackpot_bets.win')
			->where('jackpot.status', 3)
			->where(['user_id' => $user->id, 'win' => 0])
			->groupBy('jackpot_bets.game_id', 'jackpot_bets.win')
			->get()->count();
		$wheelLose = WheelBets::join('wheel', 'wheel.id', '=', 'wheel_bets.game_id')
			->select('wheel.status', 'wheel.id', 'wheel_bets.game_id')
			->where('wheel.status', 3)
			->where(['wheel_bets.user_id' => $user->id, 'wheel_bets.win' => 0])
			->groupBy('wheel_bets.game_id')
			->get()->count();
		$crashLose = CrashBets::join('crash', 'crash.id', '=', 'crash_bets.round_id')
			->select('crash.status', 'crash.id', 'crash_bets.round_id')
			->where('crash.status', 2)
			->where(['crash_bets.user_id' => $user->id, 'crash_bets.status' => 0])
			->groupBy('crash_bets.round_id')
			->get()->count();
		$coinLose1 = CoinFlip::where('winner_id', '!=', $user->id)->where('heads', $user->id)->where('status', 1)->count();
		$coinLose2 = CoinFlip::where('winner_id', '!=', $user->id)->where('tails', $user->id)->where('status', 1)->count();
		$coinLose = $coinLose1+$coinLose2;
		$battleLose = BattleBets::join('battle', 'battle.id', '=', 'battle_bets.game_id')
			->select('battle.status', 'battle.id', 'battle_bets.game_id')
			->where('battle.status', 3)
			->where(['battle_bets.user_id' => $user->id, 'battle_bets.win' => 0])
			->groupBy('battle_bets.game_id')
			->get()->count();
		$diceLose = Dice::where(['user_id' => $user->id, 'win' => 0])->count();
		$hiloLose = HiloBets::join('hilo', 'hilo.id', '=', 'hilo_bets.game_id')
			->select('hilo.status', 'hilo.id', 'hilo_bets.game_id')
			->where('hilo.status', 4)
			->where(['hilo_bets.user_id' => $user->id, 'hilo_bets.win' => 0])
			->groupBy('hilo_bets.game_id')
			->get()->count();
		$betLose = $jackpotLose+$wheelLose+$crashLose+$coinLose+$battleLose+$diceLose+$hiloLose;
		
		$info = [
			'unique_id' => $user->unique_id,
			'avatar' => $user->avatar,
			'username' => $user->username,
			'betAmount' => round($betAmount, 2),
			'totalGames' => $betCount,
			'wins' => $betWin,
			'lose' => $betLose
		];
		
		return response()->json(['success' => true, 'info' => $info]);
	}
	
	public function pay(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		if($r->get('amount') < $this->settings->min_dep) return response()->json(['success' => false, 'msg' => 'Минимальная сумма депозита '.$this->settings->min_dep.'р!', 'type' => 'error']);
		if(!$r->get('type')) return response()->json(['success' => false, 'msg' => 'Вы не выбрали платежную систему!', 'type' => 'error']);
		if($r->get('type') == 'payeer') {
			if(is_null($this->settings->payeer_mrh_ID)) return response()->json(['success' => false, 'msg' => 'Данный способ оплаты недоступен!', 'type' => 'error']);
			$m_shop = $this->settings->payeer_mrh_ID;
			$m_system = $r->get('type');
			$m_orderid = time() . mt_rand() . $this->user->id;
			$m_amount = number_format($r->get('amount'), 2, '.', '');
			$m_curr = 'RUB';
			$m_desc = base64_encode('Пополнение баланса на сайте '.$this->settings->sitename);
			$m_key = $this->settings->payeer_secret1;
			$arHash = [
				$m_shop,
				$m_orderid,
				$m_amount,
				$m_curr,
				$m_desc,
				$m_key
			];
			$sign = strtoupper(hash('sha256', implode(':', $arHash)));

			if($m_amount != 0) {
				DB::beginTransaction();
				try {
					$payment = new Payments();
					$payment->user_id = $this->user->id;
					$payment->secret = $sign;
					$payment->order_id = $m_orderid;
					$payment->sum = $m_amount;
					$payment->system = $m_system;
					$payment->save();

					DB::commit();
				} catch (\PDOException $e) {
					DB::connection()->getPdo()->rollBack();
					return response()->json(['success' => false, 'msg' => $e->getMessage(), 'type' => 'error']);
				}
			}

			return response()->json(['success' => true, 'url' => 'https://payeer.com/merchant/?m_shop='.$m_shop.'&m_orderid='.$m_orderid.'&m_amount='.$m_amount.'&m_curr='.$m_curr.'&m_desc='.$m_desc.'&m_sign='.$sign.'&lang=ru']);
			
		} elseif($r->get('type') == 'freekassa') {
			if(is_null($this->settings->fk_mrh_ID)) return response()->json(['success' => false, 'msg' => 'Данный способ оплаты недоступен!', 'type' => 'error']);
			$m_shop = $this->settings->fk_mrh_ID;
			$m_system = $r->get('type');
			$m_orderid = time() . mt_rand() . $this->user->id;
			$m_amount = number_format($r->get('amount'), 2, '.', '');
			$m_key = $this->settings->fk_secret1;
			$arHash = [
				$m_shop,
				$m_amount,
				$m_key,
				$m_orderid
			];
			
			$sign = hash('md5', implode(':', $arHash));

			if($m_amount != 0) {
				DB::beginTransaction();
				try {
					$payment = new Payments();
					$payment->user_id = $this->user->id;
					$payment->secret = $sign;
					$payment->order_id = $m_orderid;
					$payment->sum = $m_amount;
					$payment->system = $m_system;
					$payment->save();

					DB::commit();
				} catch (\PDOException $e) {
					DB::connection()->getPdo()->rollBack();
					return response()->json(['success' => false, 'msg' => $e->getMessage(), 'type' => 'error']);
				}
			}
			return response()->json(['success' => true, 'url' => 'https://www.free-kassa.ru/merchant/cash.php?m='.$m_shop.'&oa='.$m_amount.'&o='.$m_orderid.'&us_uid='.$this->user->user_id.'&s='.$sign]);
		} else {
			return response()->json(['success' => false, 'msg' => 'Ошибка!', 'type' => 'error']);
		}
	}
	
	public function resultPE(Request $r) {
        $ip = false;
        if(isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $this->getIpPE($_SERVER['HTTP_X_REAL_IP']);
        } else {
            $ip = $this->getIpPE($_SERVER['REMOTE_ADDR']);
        }
        if(!$ip) return ['msg' => 'Error check IP!', 'type' => 'error'];

        $m_shop = $r->get('m_shop');
        $m_orderid = $r->get('m_orderid');
        $m_amount = $r->get('m_amount');
        $m_curr = $r->get('m_curr');
        $m_desc = $r->get('m_desc');
        $checksum = $r->get('m_sign');
		$pay = Payments::where('order_id', $m_orderid)->first();
		
		$arHash = [
			$r->get('m_operation_id'),
			$r->get('m_operation_ps'),
			$r->get('m_operation_date'),
			$r->get('m_operation_pay_date'),
			$r->get('m_shop'),
			$r->get('m_orderid'),
			$r->get('m_amount'),
			$r->get('m_curr'),
			$r->get('m_desc'),
			$r->get('m_status')
		];
		
		//if(isset($r->get('m_params'))) $arHash[] = $r->get('m_params');
		
		$arHash[] = $this->settings->payeer_secret1;
		
		$sign_hash = strtoupper(hash('sha256', implode(':', $arHash)));

		if($r->get('m_status') != 'success') return $m_orderid.'|error';
		if(is_null($pay)) return $m_orderid.'|error';
		if($sign_hash != $checksum) return $m_orderid.'|error';
		if($pay->status != 0) return $m_orderid.'|error';
		if($pay->sum != $m_amount) return $m_orderid.'|error';
		
		if($this->settings->dep_bonus_min > 0 && $m_amount >= $this->settings->dep_bonus_min) {
			$m_amount = round($m_amount + ($m_amount/100)*$this->settings->dep_bonus_perc, 2);
			
			Profit::create([
				'game' => 'ref',
				'sum' => -(($m_amount/100)*$this->settings->dep_bonus_perc)
			]);
		}
		
		DB::beginTransaction();
		try {
			$pay->status = 1;
			$pay->update();
			$user = User::find($pay->user_id);
			$user->balance += $m_amount;
			$user->update();
			
			$this->settings->profit_money += round($m_amount/$this->settings->profit_koef, 2);
			$this->settings->save();
			
			DB::commit();

			return $m_orderid.'|success';
		} catch (\PDOException $e) {
			DB::connection()->getPdo()->rollBack();
			return $m_orderid.'|error';
		}
	}
	
	function getIpPE($ip) {
        $list = ['185.71.65.92', '185.71.65.189', '149.202.17.210', '80.71.252.176'];
        for($i = 0; $i < count($list); $i++) {
            if($list[$i] == $ip) return true;
        }
        return false;
    }
	
	public function resultFK(Request $r) {
        $ip = false;
        if(isset($_SERVER['HTTP_X_REAL_IP'])) {
            $ip = $this->getIpFK($_SERVER['HTTP_X_REAL_IP']);
        } else {
            $ip = $this->getIpFK($_SERVER['REMOTE_ADDR']);
        }
        if(!$ip) return ['msg' => 'Error check IP!', 'type' => 'error'];
        
		$order = $this->chechOrder($r->get('MERCHANT_ORDER_ID'), $r->get('AMOUNT'));
		if($order['type'] == 'error') return ['msg' => $order['msg'], 'type' => 'error'];
        
        $user = User::where('user_id', $r->get('us_uid'))->first();
        if(!$user) return ['msg' => 'User not found!', 'type' => 'error'];
        
		/* ADD Balance from user and partner */
        $sum = $r->get('AMOUNT');
		
		$this->settings->profit_money += round($sum/$this->settings->profit_koef, 2);
		$this->settings->save();
		
		if($this->settings->dep_bonus_min > 0 && $sum >= $this->settings->dep_bonus_min) {
			$sum = round($sum + ($sum/100)*$this->settings->dep_bonus_perc, 2);
			
			Profit::create([
				'game' => 'ref',
				'sum' => -(($sum/100)*$this->settings->dep_bonus_perc)
			]);
		}
		
		
        User::where('user_id', $user->user_id)->update([
            'balance' => $user->balance+$sum 
        ]);
		
        Payments::where('order_id', $r->get('MERCHANT_ORDER_ID'))->update([
            'status' => 1 
        ]);
		
        /* SUCCESS REDIRECT */
        return ['msg' => 'Your order #'.$r->get('MERCHANT_ORDER_ID').' has been paid successfully!', 'type' => 'success'];
	}
	
	private function chechOrder($id, $sum) {
		$merch = Payments::where('order_id', $id)->first();
		if(!$merch) return ['msg' => 'Order checked!', 'type' => 'success'];
		if($sum != $merch->sum) return ['msg' => 'You paid another order!', 'type' => 'error'];
		if($merch->order_id == $id && $merch->status == 1) return ['msg' => 'Order alredy paid!', 'type' => 'error'];
		
		return ['msg' => 'Order checked!', 'type' => 'success'];
	}
    
    function getIpFK($ip) {
        $list = ['136.243.38.147', '136.243.38.149', '136.243.38.150', '136.243.38.151', '136.243.38.189', '88.198.88.98', '37.1.14.226', '136.243.38.108', '80.71.252.10'];
        for($i = 0; $i < count($list); $i++) {
            if($list[$i] == $ip) return true;
        }
        return false;
    }
	
	public function userWithdraw(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 5);
		
		if($this->settings->min_dep_withdraw) {
			$dep = Payments::where('user_id', $this->user->id)->where('status', 1)->sum('sum');
			if($dep < $this->settings->min_dep_withdraw) return ['success' => false, 'msg' => 'Для вывода Вам нужно пополнить баланс минимум на '.$this->settings->min_dep_withdraw.' руб!', 'type' => 'error'];
		}
		$system = htmlspecialchars($r->get('system'));
		$wallet = htmlspecialchars($r->get('wallet'));
		$value = htmlspecialchars($r->get('value'));
		$sum = round(str_replace('/[^.0-9]/', '', $value), 2) ?? null;
		$com = null;
		$com_sum = null;
		$min = null;
		$max = 5000;
		if($system == 'payeer') {
			$com = $this->settings->payeer_com_percent; 
			$com_sum = $this->settings->payeer_com_rub; 
			$min = $this->settings->payeer_min;	
        } 
        if($system == 'qiwi') {
			$com = $this->settings->qiwi_com_percent; 
			$com_sum = $this->settings->qiwi_com_rub; 
			$min = $this->settings->qiwi_min;
        }
		if($system == 'yandex') {
			$com = $this->settings->yandex_com_percent;
			$com_sum = $this->settings->yandex_com_rub; 
			$min = $this->settings->yandex_min;
		}
		if($system == 'webmoney') {
			$com = $this->settings->webmoney_com_percent;
			$com_sum = $this->settings->webmoney_com_rub; 
			$min = $this->settings->webmoney_min;
		}
		if($system == 'visa') {
			$com = $this->settings->visa_com_percent;
			$com_sum = $this->settings->visa_com_rub; 
			$min = $this->settings->visa_min;
		}
		
		$sumCom = ($sum+($sum/100*$com))+$com_sum;
		
		if(is_null($wallet)) return ['success' => false, 'msg' => 'Не введен номер кошелька!', 'type' => 'error'];
		if(is_null($sum)) return ['success' => false, 'msg' => 'Не введена сумма вывода!', 'type' => 'error'];
		if(is_null($com)) return ['success' => false, 'msg' => 'Не удалось посчитать комиссию!', 'type' => 'error'];
		if($sum < $min) return ['success' => false, 'msg' => 'Минимальная сумма вывода '.$min.' руб!', 'type' => 'error'];
		if($sum > $max) return ['success' => false, 'msg' => 'Максимальная сумма вывода '.$max.' руб!', 'type' => 'error'];
		
		if($sumCom > $this->user->balance) return ['success' => false, 'msg' => 'Не хватает средств для вывода!', 'type' => 'error'];
		
		Withdraw::insert([
            'user_id' => $this->user->id,
            'value' => $sum,
            'valueWithCom' => $sumCom,
            'system' => $system,
            'wallet' => $wallet
        ]);
		
		$this->user->balance -= $sumCom;
		$this->user->requery -= $sumCom;
		$this->user->save();
		
		$this->redis->publish('updateBalance', json_encode([
            'unique_id'    => $this->user->unique_id,
            'balance' => round($this->user->balance, 2)
        ]));
		
        return ['success' => true, 'msg' => 'Выплата произведена на указанный кошелек!', 'type' => 'success'];
	}
	
	public function success() {
		return redirect()->route('index')->with('success', 'Ваш баланс успешно пополнен!');
	}
	
	public function fail() {
		return redirect()->route('index')->with('error', 'Ошибка при пополнении баланса!');
	}
	
	public function userWithdrawCancel(Request $r) {
		if(\Cache::has('action.user.' . $this->user->id)) return response()->json(['success' => false, 'msg' => 'Подождите перед предыдущим действием!', 'type' => 'error']);
        \Cache::put('action.user.' . $this->user->id, '', 2);
		$id = $r->get('id');
        $withdraw = Withdraw::where('id', $id)->first();
		
		if($withdraw->status > 0) return response()->json(['success' => false, 'msg' => 'Вы не можете отменить данный вывод!', 'type' => 'error']);
		if($withdraw->user_id != $this->user->id) return response()->json(['success' => false, 'msg' => 'Вы не можете отменить вывод другого пользователя!', 'type' => 'error']);
		
		$this->user->balance += $withdraw->valueWithCom;
		$this->user->requery += $withdraw->valueWithCom;
        $this->user->save();
        $withdraw->status = 2;
        $withdraw->save();
		
		return response()->json(['success' => true, 'msg' => 'Вы отменили вывод на '.$withdraw->valueWithCom.'р.', 'type' => 'success', 'id' => $id]);
	}
	
	private function groupIsMember($id) {
        $user_id = $id;
        $vk_url = $this->settings->vk_url;
        if(!$vk_url) $group = NULL;
        $old_url = ($vk_url);
        $url = explode('/', trim($old_url,'/'));
        $url_parse = array_pop($url);
        $url_last = preg_replace('/&?club+/i', '', $url_parse);
        $group = $this->curl('https://api.vk.com/method/groups.isMember?v=5.3&group_id='.$url_last.'&user_id='.$user_id.'&access_token='.$this->settings->vk_service_key);
        
        if(isset($group['error'])) {
            $group = NULL;
        } else {
            $group = $group['response'];
        }
        return $group;
    }
	
	private function curl($url) {
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $group = curl_exec($ch);
        curl_close($ch);
        return json_decode($group, true);
	}
}