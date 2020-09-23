<?php namespace App\Http\Controllers;

use Socialite;
use App\User;
use App\Profit;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller {

    public function __construct() {
        parent::__construct();
    }
	
    public function login($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
		try { 
			$user = json_decode(json_encode(Socialite::driver($provider)->stateless()->user())); 
		} catch (\Exception $e) { 
			return redirect()->route('index')->with('error', 'Данные сессии устарели, попробуйте еще раз!'); 
		}
        if(isset($user->returnUrl)) return redirect('/');
        $user = $user->user;
        $user = $this->createOrGetUser($user, $provider);
        Auth::login($user, true);
        return redirect()->intended('/');
    }

    public function createOrGetUser($user, $provider) {
		$ref_id = Cookie::get('ref');
		$ref = User::where('unique_id', $ref_id)->first();
		$plus = 0;
		$ban = 0;
		$ban_reason = null;
        if ($provider == 'vkontakte') {
			$ipCheck = User::where('ban', 0)->where('ip', request()->ip())->first();
			$banCheck = User::where('ban', 1)->where('user_id', $user->id)->count();
			if(!is_null($ipCheck) && $ipCheck->user_id != $user->id ||  $banCheck > 0) {
				$ban_reason = 'Нарушение правила п. 7.4.1. - "Запрещено регистрировать более одного аккаунта через сайт. Такие действия повлекут к блокировке аккаунта"';
			}
            $u = User::where('user_id', $user->id)->first();
            if ($u) {
                $username = $user->first_name.' '.$user->last_name;
                User::where('user_id', $user->id)->update([
                    'username' => $username,
                    'avatar' => $user->photo_max,
                    'ip' => request()->ip(),
                    'ban' => $ban,
                    'ban_reason' => $ban_reason
                ]); 
                $user = $u;
            } else {
				if(!is_null($ref) && !$ban) {
					$ref->link_reg += 1;
					$ref->save();
					$plus = $this->settings->ref_sum;
					if($plus > 0) Profit::create([
						'game' => 'ref',
						'sum' => -$plus
					]);
				} else $ref_id = null;
                $username = $user->first_name.' '.$user->last_name;
                $user = User::create([
                    'unique_id' => bin2hex(random_bytes(6)),
                    'user_id' => $user->id,
                    'username' => $username,
                    'avatar' => $user->photo_max,
                    'ip' => request()->ip(),
                    'balance' => $plus,
                    'ref_id' => $ref_id,
                    'ban' => $ban,
                    'ban_reason' => $ban_reason
                ]);
            }
        }
        return $user;
    }

    public function logout()
    {
		Cache::flush();
        Auth::logout();
		Session::flush();
        return redirect()->intended('/');
    }
}