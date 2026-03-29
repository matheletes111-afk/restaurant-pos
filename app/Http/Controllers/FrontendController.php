<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RestaurantMaster;
use Auth;
class FrontendController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function create()
    {
        return view('register_restaurant');
    }

    public function store(Request $request)
{
    $request->validate([
        'restaurant_name' => 'required',
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'phone' => 'required',
        'address' => 'required',
        'pincode' => 'required',
        'password' => 'required|min:6',
    ]);

    // 1. Create Owner User
    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->password = \Hash::make($request->password);
    $user->role = 'RES';
    $user->role_type = 'ADMIN';
    $user->status = 'A';
    $user->save();

    // 2. Create Restaurant
    $restaurant = new RestaurantMaster();
    $restaurant->name = $request->restaurant_name;
    $restaurant->address = $request->address;
    $restaurant->pincode = $request->pincode;
    $restaurant->owner_id = $user->id;
    $restaurant->status = 'A';
    $restaurant->created_by = $user->id;
    $restaurant->save();

    // 3. Update user with restaurant ID
    User::where('id', $user->id)->update(['restaurant_id' => $restaurant->id]);

    // 4. Auto login the user
    Auth::login($user);

    // 5. Redirect to plan selection page
    return redirect()->route('select.plan.page')->with('success', 'Registration successful! Please choose a plan.');
}
}
