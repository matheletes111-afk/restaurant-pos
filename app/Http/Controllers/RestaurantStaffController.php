<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Hash;

class RestaurantStaffController extends Controller
{
    // SHOW PAGE
    public function index()
    {
        $data = User::where('restaurant_id', auth()->user()->restaurant_id)
                ->where('role_type','!=','ADMIN')
                ->orderBy('id', 'DESC')
                ->where('status','!=','D')
                ->get();

        return view('staff', compact('data'));
    }

    // INSERT
    public function insert(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email|unique:users,email',
            'phone'     => 'required',
            'role_type' => 'required',
            'password'  => 'required',
        ]);

        $user = new User;
        $user->name      = $request->name;
        $user->email     = $request->email;
        $user->role      = 'RES';
        $user->phone     = $request->phone;
        $user->role_type = $request->role_type;
        $user->restaurant_id = auth()->user()->restaurant_id;
        $user->address   = $request->address;
        $user->pincode   = $request->pincode;
        $user->status    = $request->status;
        $user->password  = Hash::make($request->password);

        $user->save();

        return back()->with('success','Staff added successfully!');
    }

    // UPDATE
    public function update(Request $request)
    {
        $request->validate([
            'name'      => 'required',
            'email'     => 'required|email',
            'phone'     => 'required',
            'role_type' => 'required',
        ]);

        $user = User::find($request->id);

        if(!$user){ return back()->with('error','Staff not found!'); }

        $user->name      = $request->name;
        $user->email     = $request->email;
        $user->phone     = $request->phone;
        $user->role_type = $request->role_type;
        $user->address   = $request->address;
        $user->pincode   = $request->pincode;
        $user->status    = $request->status;

        $user->save();

        return back()->with('success','Staff updated successfully!');
    }

    // DELETE
    public function delete($id)
    {
        User::where('id', $id)->update(['status'=>'D']);
        return back()->with('success','Staff deleted successfully!');
    }

    public function status($id)
    {
        $check = User::where('id', $id)->first();
        if (@$check->status=="A") {
            User::where('id', $id)->update(['status'=>'I']);
        }else{
            User::where('id', $id)->update(['status'=>'A']);
        }
        return back()->with('success','Staff status changed successfully!');
    }

    public function permissions($id)
    {
        if (auth()->user()->role_type !== 'ADMIN') {
            abort(403, 'Only restaurant administrators can manage staff permissions.');
        }

        $staff = User::where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();

        $menus = [
            [
                'key' => 'menu_master',
                'title' => 'Menu Master',
                'description' => 'Manage food categories and add/edit/delete dishes.',
                'icon' => 'fas fa-concierge-bell'
            ],
            [
                'key' => 'menu_availability',
                'title' => 'Menu Availability',
                'description' => 'Toggle food availability status and manage discounts.',
                'icon' => 'fas fa-clipboard-list'
            ],
            [
                'key' => 'table_master',
                'title' => 'Table Master',
                'description' => 'Manage restaurant table layouts, details, and QR codes.',
                'icon' => 'fas fa-chair'
            ],
            [
                'key' => 'order_master',
                'title' => 'Order Master',
                'description' => 'Access to creating, editing, and managing orders and processing payments.',
                'icon' => 'fas fa-receipt'
            ],
            [
                'key' => 'kitchen_order',
                'title' => 'Kitchen Order',
                'description' => 'Access to the kitchen panel to view and process active food items.',
                'icon' => 'fas fa-users'
            ],
            [
                'key' => 'pending_order',
                'title' => 'Pending Order',
                'description' => 'Approve or reject customer-initiated QR orders.',
                'icon' => 'fas fa-clock'
            ],
            [
                'key' => 'restro_ai',
                'title' => 'Restro AI',
                'description' => 'Interact with the AI Chat assistant for restro analytics and help.',
                'icon' => 'fas fa-robot'
            ],
            [
                'key' => 'billing_subscription',
                'title' => 'Billing & Subscription',
                'description' => 'View current active plan, billing history, and handle subscription renewals.',
                'icon' => 'fas fa-credit-card'
            ],
            [
                'key' => 'customer_support',
                'title' => 'Customer Support',
                'description' => 'Create support tickets and view responses from customer support.',
                'icon' => 'fas fa-headset'
            ],
            [
                'key' => 'staff',
                'title' => 'Staff Management',
                'description' => 'Add new staff members, toggle status, or delete staff records.',
                'icon' => 'fas fa-users'
            ],
            [
                'key' => 'inventory_setting',
                'title' => 'Inventory Setting',
                'description' => 'Access to units, products, suppliers, purchases, stockouts, and debit notes.',
                'icon' => 'fas fa-boxes'
            ],
            [
                'key' => 'reports',
                'title' => 'Reports & Analytics',
                'description' => 'Access top dish/customer analysis, order reports, live stock, and order graphs.',
                'icon' => 'ti ti-report-analytics'
            ]
        ];

        $selectedPermissions = $staff->permissions ?: [];

        return view('restaurant.permissions', compact('staff', 'menus', 'selectedPermissions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        if (auth()->user()->role_type !== 'ADMIN') {
            abort(403, 'Only restaurant administrators can manage staff permissions.');
        }

        $staff = User::where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();

        $staff->permissions = $request->input('permissions', []);
        $staff->save();

        return redirect()->route('restaurant.staff.index')->with('success', 'Staff permissions updated successfully!');
    }
}
