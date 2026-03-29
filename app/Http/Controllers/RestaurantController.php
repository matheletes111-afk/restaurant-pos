<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RestaurantMaster;

class RestaurantController extends Controller
{
    public function index()
    {
        // Fetch restaurants with owner details
        $data['restaurants'] = RestaurantMaster::where('status', '!=', 'D')
            ->with('owner') // requires relationship in model
            ->get();

        return view('restaurant.index', $data);
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
            'password' => 'required',
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
        $restaurant->created_by = auth()->id();
        $restaurant->save();

        User::where('id',$user->id)->update(['restaurant_id'=>$restaurant->id]);

        return redirect()->back()->with('success', 'Restaurant added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'restaurant_name' => 'required',
            'address' => 'required',
            'pincode' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $request->owner_id,
            'phone' => 'required',
        ]);

        $restaurant = RestaurantMaster::find($request->id);
        if (!$restaurant) {
            return back()->with('error', 'Restaurant not found.');
        }

        // Update Owner User
        $user = User::find($restaurant->owner_id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->save();

        // Update Restaurant
        $restaurant->name = $request->restaurant_name;
        $restaurant->address = $request->address;
        $restaurant->pincode = $request->pincode;
        $restaurant->updated_by = auth()->id();
        $restaurant->save();

        return redirect()->back()->with('success', 'Restaurant updated successfully.');
    }

    // -------------------------------------------------------------------------
    // CHANGE STATUS (Active / Inactive)
    // -------------------------------------------------------------------------
    public function status($owner_id)
    {
        $user = User::find($owner_id);

        if ($user) {
            $newStatus = $user->status === 'A' ? 'I' : 'A';
            $user->status = $newStatus;
            $user->save();

            // Update restaurant status also
            RestaurantMaster::where('owner_id', $owner_id)->update([
                'status' => $newStatus
            ]);

            return back()->with('success', 'Status updated successfully.');
        }

        return back()->with('error', 'Record not found.');
    }

    // -------------------------------------------------------------------------
    // DELETE OWNER + RESTAURANT (Soft delete = status D)
    // -------------------------------------------------------------------------
    public function delete($owner_id)
    {
        $user = User::find($owner_id);

        if ($user) {
            $user->status = 'D';
            $user->save();

            // Delete restaurant also
            RestaurantMaster::where('owner_id', $owner_id)->update([
                'status' => 'D'
            ]);

            return back()->with('success', 'Restaurant deleted successfully.');
        }

        return back()->with('error', 'Record not found.');
    }
}
