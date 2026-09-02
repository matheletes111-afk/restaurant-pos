<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use App\Mail\ResetPassword;
use App\Mail\LoginOtpMail;
use Auth;
use Mail;
use Illuminate\Http\Request;
use DB;
use Hash;
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
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }


    public function customLogin(Request $request)
    {
       $userDataEmail=User::where('email',$request->email)->whereIn('status',['A','Active'])->first();
        // return $request;
        if ($userDataEmail) {
           if (!\Hash::check($request->password,$userDataEmail->password)) {
               return redirect()->back()->with('error','Incorrect Password');
            }

            // Generate a random 6-digit OTP
            $otp = rand(100000, 999999);

            // Store user id, otp, and expires_at in session
            session([
                'login_2fa_user_id' => $userDataEmail->id,
                'login_2fa_otp' => $otp,
                'login_2fa_otp_expires_at' => now()->addMinutes(10),
            ]);

            $mailData = [
                'email' => $userDataEmail->email,
                'name' => $userDataEmail->name,
                'otp' => $otp,
            ];

            try {
                Mail::send(new LoginOtpMail($mailData));
            } catch (\Exception $e) {
                // If mail sending fails, clear session and show error
                session()->forget(['login_2fa_user_id', 'login_2fa_otp', 'login_2fa_otp_expires_at']);
                return redirect()->back()->with('error', 'Failed to send OTP email: ' . $e->getMessage());
            }

            return redirect()->route('login.verify')->with('success', 'A verification code has been sent to your email.');
        }else{
            return redirect()->back()->with('error','Wrong Credentials Are Given');
        }
    }

    public function showVerifyForm(Request $request)
    {
        if (!session()->has('login_2fa_user_id')) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        return view('auth.verify_otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        if (!session()->has('login_2fa_user_id') || !session()->has('login_2fa_otp') || !session()->has('login_2fa_otp_expires_at')) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        if (now()->greaterThan(session('login_2fa_otp_expires_at'))) {
            return redirect()->route('login.verify')->with('error', 'OTP has expired. Please request a new one.');
        }

        if ($request->otp != session('login_2fa_otp')) {
            return redirect()->route('login.verify')->with('error', 'Invalid OTP code.');
        }

        $userId = session('login_2fa_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Authenticate the user
        Auth::login($user);

        // Clear 2FA session variables
        session()->forget(['login_2fa_user_id', 'login_2fa_otp', 'login_2fa_otp_expires_at']);

        // Redirect logic matching previous customLogin
        if ($user->role == "SA") {
            return redirect()->route('admin.dashboard');
        }
        
        $active = DB::table('subscriptions')
            ->where('user_id', $user->restaurant_id)
            ->whereIn('status', ['active', 'completed'])
            ->first();
            
        if (@$active == "") {
            return redirect()->route('select.plan.page');
        } else {
            return redirect()->route('dashboard');
        }
    }

    public function resendOtp(Request $request)
    {
        if (!session()->has('login_2fa_user_id')) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        $userId = session('login_2fa_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Generate a new 6-digit OTP
        $otp = rand(100000, 999999);

        // Update session
        session([
            'login_2fa_otp' => $otp,
            'login_2fa_otp_expires_at' => now()->addMinutes(10),
        ]);

        $mailData = [
            'email' => $user->email,
            'name' => $user->name,
            'otp' => $otp,
        ];

        try {
            Mail::send(new LoginOtpMail($mailData));
        } catch (\Exception $e) {
            return redirect()->route('login.verify')->with('error', 'Failed to send OTP email: ' . $e->getMessage());
        }

        return redirect()->route('login.verify')->with('success', 'A new OTP has been sent to your email.');
    }

    public function logout(Request $request)
    {

        Auth::logout();
        return redirect('/login');
    }

            public function forgetPassword()
    {
        return view('auth.forget_password');
    }

    public function forgetPasswordSubmit(Request $request)
    {
        $getdata = User::where('email',$request->email)->first();
        if ($getdata === null) {
           return back()->with('error','This email is not registered yet');
        }else{
            $otp = rand(10000, 99999);
            $update_vcode = User::where('email',$request->email)->update(['email_vcode'=>$otp]);
            $get_vcode = User::where('email',$request->email)->first();
             $data = [
                'email'=>$request->email,
                'name'=>$get_vcode->name,
                'email_vcode'=>$get_vcode->email_vcode,
                'id'=>$get_vcode->id,
                
            ];
            Mail::send(new ResetPassword($data));
            return redirect()->route('forget.password.portal.forget.password.mail.verify',$get_vcode->id)->with('success','An Otp send to your email');
        }
    }

    public function forgetPasswordMailVerify($id)
    {
       $data = User::where('id',$id)->first();
       if ($data===null) {
           return redirect()->route('login')->with('error','Link expired');
       }
       return view('auth.reset_password',compact('data'));
    }

    public function enterNewPassword(Request $request)
    {
        $check = User::where('id',$request->id)->where('email_vcode',$request->email_vcode)->first();
        if (@$check=="") {
            return redirect()->back()->with('error','Invalid Otp');
        }
        $password = $request->input('password'); 
       
        $updatepassword = User::where('id',$request->id)->update([
            'password'=>Hash::make($password),
            'email_vcode'=>''
        ]); 

        return redirect()->route('login')->with('success','Password changed successfully');
    }
}
