<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use DB;
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
}
