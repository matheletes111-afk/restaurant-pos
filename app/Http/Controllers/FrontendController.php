<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\DemoLead;
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

    public function bookDemo(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'restaurant_name' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:20',
            'email_address' => 'required|email|max:255',
            'source' => 'nullable|string|max:255',
        ]);

        try {
            DemoLead::create([
                'full_name' => $request->full_name,
                'restaurant_name' => $request->restaurant_name,
                'phone_number' => $request->phone_number,
                'email_address' => $request->email_address,
                'source' => $request->source,
                'status' => 'Contacted',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your demo request has been submitted successfully. We will reach out to you shortly.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
