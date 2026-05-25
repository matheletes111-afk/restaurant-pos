<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestaurantMaster;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RestaurantProfileController extends Controller
{
    /**
     * Show restaurant profile
     */
    public function showProfile()
    {
        $restaurant = RestaurantMaster::with('owner')
            ->where('id', auth()->user()->restaurant_id)
            ->firstOrFail();
        
        return view('restaurant.profile', compact('restaurant'));
    }
    
    /**
     * Update restaurant profile
     */
    public function updateProfile(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'restaurant_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'gstin' => 'nullable|string|max:50',
            'gst_percentage' => 'nullable|numeric|min:0|max:100'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Get restaurant
            $restaurant = RestaurantMaster::where('id', auth()->user()->restaurant_id)->firstOrFail();
            
            // Get user (owner)
            $user = User::find($restaurant->owner_id);
            
            // Update User Table (Phone only - email is readonly)
            $user->phone = $request->phone;
            $user->save();
            
            // Update Restaurant Master
            $restaurant->name = $request->restaurant_name;
            $restaurant->address = $request->address;
            $restaurant->pincode = $request->pincode;
            $restaurant->gstin = $request->gstin;
            $restaurant->gst_percentage = $request->gst_percentage ?? 0;
            $restaurant->save();
            
            DB::commit();
            
            return redirect()->back()->with('success', 'Profile updated successfully');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            $user = User::find(auth()->id());
            
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Current password is incorrect');
            }
            
            $user->password = Hash::make($request->new_password);
            $user->save();
            
            return redirect()->back()->with('success', 'Password updated successfully');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}