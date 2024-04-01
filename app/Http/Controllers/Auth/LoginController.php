<?php

namespace App\Http\Controllers\Auth;

use Event;
use App\Models\User;
use App\Models\UserLog;
use App\Events\SendMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public $maxAttempts = 3;
    public $decayMinutes = 1;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function sendLoginResponse(Request $request) 
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $userobj = User::where('email',$request->email)->first();
        
        if($userobj)
        {
            $user_log = new UserLog;
            $user_log->user_email = $request->email;
            $user_log->ip_address = $ip;
            $user_log->user_agent = $userAgent;
            $user_log->status = 'success';
            $user_log->url = $actual_link;
            $user_log->save();
        }
        
        // Event::dispatch(new SendMail($userobj));
        // event(new SendMail($userobj));

        return redirect('/home')
            ->withInput($request->only($this->username(), 'remember'));

    }

    protected function sendFailedLoginResponse(Request $request)
    {    
        $errors = [$this->username() => trans('auth.failed')];

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        $userobj = User::where('email',$request->email)->first();
        
        if($userobj)
        {
            $user_log = new UserLog;
            $user_log->user_email = $request->email;
            $user_log->ip_address = $ip;
            $user_log->user_agent = $userAgent;
            $user_log->status = 'failed';
            $user_log->url = $actual_link;
            $user_log->save();
        }

        return redirect('/login')
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }
        

    protected function sendLockoutResponse(Request $request)
    {    
        $errors = [$this->username() => trans('auth.throttle')];

        $userAgent = $_SERVER['HTTP_USER_AGENT'];
        $ip = $_SERVER['REMOTE_ADDR'];

        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        
        $userobj = User::where('email',$request->email)->first();
        if($userobj)
        {
            $user_log = new UserLog;
            $user_log->user_email = $request->email;
            $user_log->ip_address = $ip;
            $user_log->user_agent = $userAgent;
            $user_log->status = 'failed';
            $user_log->url = $actual_link;
            $user_log->save();
        }

        // return redirect('/login')
        //     ->withInput($request->only($this->username(), 'remember'))
        //     ->withErrors($errors);
        $seconds = $this->limiter()->availableIn($this->throttleKey($request));

            return $request->expectsJson()
                    ? response()->json(['message' => 'Too many login attempts. Please try again in ' . $seconds . ' seconds.'], 429)
                    : redirect()->route('login')->withErrors(['email' => trans('auth.throttle', ['seconds' => $seconds])]);
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/login');
    }
}
