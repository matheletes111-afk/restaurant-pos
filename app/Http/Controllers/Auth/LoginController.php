<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use App\Mail\ResetPassword;
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
       $userDataEmail=User::where('email',$request->email)->first();
        // return $request;
        if ($userDataEmail) {


          
           if (!\Hash::check($request->password,$userDataEmail->password)) {
               return redirect()->back()->with('error','Incorrect Password');
            }


   
            
            Auth::login($userDataEmail);


            if ($userDataEmail->role=="SA") {
                return redirect()->route('manage.restaurant');
            }
            $active = DB::table('subscriptions')->where('user_id',$userDataEmail->restaurant_id)->where('status','active')->first();
            if(@$active=="")
            {
                return redirect()->route('select.plan.page');
            }else{
                return redirect()->route('dashboard');
            }

            
            
        }else{
            return redirect()->back()->with('error','Wrong Credentials Are Given');
        }
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
            $update_vcode = User::where('email',$request->email)->update(['email_vcode'=>time()]);
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
