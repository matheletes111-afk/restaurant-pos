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
        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $data = User::whereIn('restaurant_id', $outletIds)
                ->where('role_type','!=','ADMIN')
                ->where('status','!=','D')
                ->with('restaurant')
                ->orderBy('id', 'DESC')
                ->get();

        return view('staff', compact('data', 'availableOutlets'));
    }

    // INSERT
    public function insert(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'required',
            'role_type'     => 'required',
            'password'      => 'required',
            'restaurant_id' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $targetRestaurantId = $request->restaurant_id ?: $user->restaurant_id;

        // Verify target restaurant belongs to this owner
        $availableOutlets = $user->getAvailableOutlets();
        if ($availableOutlets->isNotEmpty() && !$availableOutlets->contains('id', $targetRestaurantId)) {
            return back()->with('error', 'Invalid outlet selected.');
        }

        $newUser = new User;
        $newUser->name          = $request->name;
        $newUser->email         = $request->email;
        $newUser->role          = 'RES';
        $newUser->phone         = $request->phone;
        $newUser->role_type     = $request->role_type;
        $newUser->restaurant_id = $targetRestaurantId;
        $newUser->address       = $request->address;
        $newUser->pincode       = $request->pincode;
        $newUser->status        = $request->status ?? 'A';
        $newUser->password      = Hash::make($request->password);

        $newUser->save();

        return back()->with('success','Staff added successfully!');
    }

    // UPDATE
    public function update(Request $request)
    {
        $request->validate([
            'name'          => 'required',
            'email'         => 'required|email',
            'phone'         => 'required',
            'role_type'     => 'required',
            'restaurant_id' => 'nullable|integer',
        ]);

        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staffUser = User::where('id', $request->id)
            ->whereIn('restaurant_id', $outletIds)
            ->first();

        if(!$staffUser){ return back()->with('error','Staff not found!'); }

        $targetRestaurantId = $request->restaurant_id ?: $staffUser->restaurant_id;
        if ($availableOutlets->isNotEmpty() && !$availableOutlets->contains('id', $targetRestaurantId)) {
            return back()->with('error', 'Invalid outlet selected.');
        }

        $staffUser->name          = $request->name;
        $staffUser->email         = $request->email;
        $staffUser->phone         = $request->phone;
        $staffUser->role_type     = $request->role_type;
        $staffUser->restaurant_id = $targetRestaurantId;
        $staffUser->address       = $request->address;
        $staffUser->pincode       = $request->pincode;
        $staffUser->status        = $request->status;

        $staffUser->save();

        return back()->with('success','Staff updated successfully!');
    }

    // DELETE
    public function delete($id)
    {
        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        User::where('id', $id)
            ->whereIn('restaurant_id', $outletIds)
            ->update(['status'=>'D']);

        return back()->with('success','Staff deleted successfully!');
    }

    public function status($id)
    {
        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $check = User::where('id', $id)->whereIn('restaurant_id', $outletIds)->first();
        if ($check) {
            $newStatus = ($check->status == "A") ? "I" : "A";
            User::where('id', $id)->update(['status' => $newStatus]);
            return back()->with('success','Staff status changed successfully!');
        }
        return back()->with('error', 'Staff member not found.');
    }

    public function permissions($id)
    {
        if (auth()->user()->role_type !== 'ADMIN') {
            abort(403, 'Only restaurant administrators can manage staff permissions.');
        }

        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staff = User::where('id', $id)
            ->whereIn('restaurant_id', $outletIds)
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
                'key' => 'cash_drawer',
                'title' => 'Cash Drawer Management',
                'description' => 'Access cash drawer ledger, record cash in / cash out, and monitor live balance.',
                'icon' => 'fas fa-cash-register'
            ],
            [
                'key' => 'expense_management',
                'title' => 'Expense Management',
                'description' => 'Record daily restaurant expenses with payment modes and track spending.',
                'icon' => 'fas fa-wallet'
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

        $user = auth()->user();
        $availableOutlets = $user->getAvailableOutlets();
        $outletIds = $availableOutlets->isNotEmpty() ? $availableOutlets->pluck('id')->toArray() : [$user->restaurant_id];

        $staff = User::where('id', $id)
            ->whereIn('restaurant_id', $outletIds)
            ->firstOrFail();

        $staff->permissions = $request->input('permissions', []);
        $staff->save();

        return redirect()->route('restaurant.staff.index')->with('success', 'Staff permissions updated successfully!');
    }
}
