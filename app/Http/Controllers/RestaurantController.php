<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RestaurantMaster;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\RestaurantRegistrationMail;
use App\Models\Plan;
use App\Models\RestaurantToCustomPlan;
class RestaurantController extends Controller
{
    /**
     * Display list of restaurants
     */
    public function index()
    {
        // Fetch restaurants with owner details
        $data['restaurants'] = RestaurantMaster::where('status', '!=', 'D')
            ->with('owner') // requires relationship in model
            ->get();

        return view('restaurant.index', $data);
    }

    /**
     * Store new restaurant with owner
     */
    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            // Restaurant Information
            'restaurant_name' => 'required|string|max:255',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            
            // Owner Information
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6',
        ], [
            'restaurant_name.required' => 'Restaurant name is required',
            'address.required' => 'Address is required',
            'pincode.required' => 'Pincode is required',
            'name.required' => 'Owner name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'This email is already registered',
            'phone.required' => 'Phone number is required',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters',
        ]);

        DB::beginTransaction();
        
        try {
            // Store plain password for email
            $plainPassword = $request->password;
            
            // 1. Create Owner User
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->role = 'RES'; // Restaurant role
            $user->role_type = 'ADMIN';
            $user->status = 'A';
            $user->created_by = auth()->id();
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

            // 3. Update user with restaurant_id
            $user->restaurant_id = $restaurant->id;
            $user->save();

            // 4. Send Welcome Email with credentials
            try {
                Mail::to($user->email)->send(new RestaurantRegistrationMail($user, $plainPassword, $restaurant));
            } catch (\Exception $mailError) {
                // Log email error but don't rollback the transaction
                \Log::error('Failed to send registration email: ' . $mailError->getMessage());
            }

            DB::commit();

            return redirect()->back()->with('success', 'Restaurant added successfully. Login credentials have been sent to ' . $user->email);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error adding restaurant: ' . $e->getMessage());
        }
    }

    /**
     * Update restaurant and owner details
     */
    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:restaurant_masters,id',
            'owner_id' => 'required|exists:users,id',
            'restaurant_name' => 'required|string|max:255',
            'address' => 'required|string',
            'pincode' => 'required|string|max:10',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->owner_id,
            'phone' => 'required|string|max:15',
        ]);

        DB::beginTransaction();
        
        try {
            // Update Restaurant
            $restaurant = RestaurantMaster::find($request->id);
            if (!$restaurant) {
                return back()->with('error', 'Restaurant not found.');
            }
            
            $restaurant->name = $request->restaurant_name;
            $restaurant->address = $request->address;
            $restaurant->pincode = $request->pincode;
            $restaurant->updated_by = auth()->id();
            $restaurant->save();

            // Update Owner User
            $user = User::find($restaurant->owner_id);
            if ($user) {
                $user->name = $request->name;
                $user->email = $request->email;
                $user->phone = $request->phone;
                $user->save();
            }

            DB::commit();

            return redirect()->back()->with('success', 'Restaurant updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating restaurant: ' . $e->getMessage());
        }
    }

    /**
     * Change restaurant status (Active/Inactive)
     */
    public function status($owner_id)
    {
        $user = User::find($owner_id);

        if ($user) {
            DB::beginTransaction();
            try {
                $newStatus = $user->status === 'A' ? 'I' : 'A';
                $user->status = $newStatus;
                $user->save();

                // Update restaurant status also
                RestaurantMaster::where('owner_id', $owner_id)->update([
                    'status' => $newStatus,
                    'updated_by' => auth()->id()
                ]);

                DB::commit();
                return back()->with('success', 'Status updated successfully.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error updating status.');
            }
        }

        return back()->with('error', 'Record not found.');
    }

    /**
     * Delete restaurant (Soft delete - status 'D')
     */
    public function delete($id)
    {
        $restaurant = RestaurantMaster::find($id);
        
        if (!$restaurant) {
            return back()->with('error', 'Restaurant not found.');
        }

        DB::beginTransaction();
        
        try {
            // Update restaurant status to 'D' for soft delete
            $restaurant->status = 'D';
            $restaurant->updated_by = auth()->id();
            $restaurant->save();

            // Update owner status to 'D'
            if ($restaurant->owner_id) {
                User::where('id', $restaurant->owner_id)->update([
                    'status' => 'D'
                ]);
            }

            DB::commit();
            return back()->with('success', 'Restaurant deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting restaurant: ' . $e->getMessage());
        }
    }

    /**
     * Restaurant Analytics/Dashboard
     */
    public function analytics($id)
    {
        $restaurant = RestaurantMaster::with('owner')->find($id);
        
        if (!$restaurant) {
            return redirect()->back()->with('error', 'Restaurant not found.');
        }
        
        // You can add analytics data here
        $data = [
            'restaurant' => $restaurant,
            'total_orders' => $restaurant->orders()->count(),
            'total_revenue' => $restaurant->orders()->sum('grand_total'),
            'total_customers' => $restaurant->orders()->distinct('customer_name')->count('customer_name'),
        ];
        
        return view('restaurant.analytics', $data);
    }

        /**
     * Show plan assignment page for specific restaurant
     */
    public function showPlans($id)
    {
        // Get restaurant details
        $restaurant = RestaurantMaster::with('owner')->findOrFail($id);
        
        // Get all custom plans (where is_default_free = 'N')
        $plans = Plan::where('is_delete', 'N')
            ->where('is_default_free', 'N')
            ->orderBy('name', 'asc')
            ->get();
        
        // Get already assigned plan IDs for this restaurant
        $assignedPlanIds = RestaurantToCustomPlan::where('restaurant_id', $id)
            ->pluck('plan_id')
            ->toArray();
        
        return view('restaurant.assign-plans', compact('restaurant', 'plans', 'assignedPlanIds'));
    }
    
    /**
     * Save assigned plans for restaurant
     */
    public function savePlans(Request $request)
    {
      
        
        try {
            DB::beginTransaction();
            
            // Delete all existing assignments
            RestaurantToCustomPlan::where('restaurant_id', $request->restaurant_id)->delete();
            
            // Add new assignments
            if ($request->has('plan_ids') && is_array($request->plan_ids)) {
                foreach ($request->plan_ids as $planId) {
                    RestaurantToCustomPlan::create([
                        'restaurant_id' => $request->restaurant_id,
                        'plan_id' => $planId,
                        'created_by' => auth()->id()
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('manage.restaurant.show.plans', $request->restaurant_id)
                ->with('success', 'Plans assigned successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}