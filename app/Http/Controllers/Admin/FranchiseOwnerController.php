<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\RestaurantMaster;
use App\Models\FranchiseRestaurantMapping;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class FranchiseOwnerController extends Controller
{
    /**
     * Check authorization for Admin Panel
     */
    private function authorizeAdmin()
    {
        if (!auth()->check() || auth()->user()->role == 'RES') {
            abort(403, 'Unauthorized access. Only admin users can manage Franchise Sponsorship Owners.');
        }
    }

    /**
     * List all Franchise Sponsorship Owners (Role: FSO)
     */
    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $owners = User::where('role', 'FSO')
            ->withCount(['franchiseRestaurants' => function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('restaurant_master.parent_id')
                        ->orWhere('restaurant_master.parent_id', 0);
                })->where('restaurant_master.status', '!=', 'D');
            }])
            ->orderBy('id', 'desc')
            ->get();

        $totalOwners = $owners->count();
        $activeOwners = $owners->filter(function ($u) {
            return in_array(strtoupper($u->status ?? 'A'), ['A', 'ACTIVE', '1']);
        })->count();
        $inactiveOwners = $totalOwners - $activeOwners;
        $newThisMonth = $owners->filter(function ($u) {
            return $u->created_at && Carbon::parse($u->created_at)->isCurrentMonth();
        })->count();

        $summary = [
            'total' => $totalOwners,
            'active' => $activeOwners,
            'inactive' => $inactiveOwners,
            'new_this_month' => $newThisMonth,
        ];

        // Only show parent restaurants (exclude sub-restaurants/outlets)
        $allRestaurants = RestaurantMaster::where('status', '!=', 'D')
            ->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'restaurant_id_unique', 'address', 'status']);

        return view('admin.fso.index', compact('owners', 'summary', 'allRestaurants'));
    }

    /**
     * Store a newly created Franchise Sponsorship Owner
     */
    public function store(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'required|string|min:6',
            'status' => 'nullable|in:A,I',
        ], [
            'name.required' => 'Please enter the owner full name.',
            'email.required' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered in the system.',
            'phone.required' => 'Please enter the contact phone number.',
            'password.required' => 'Please provide an account password (minimum 6 characters).',
            'password.min' => 'The password must be at least 6 characters long.',
        ]);

        $owner = new User();
        $owner->name = trim($request->name);
        $owner->email = strtolower(trim($request->email));
        $owner->phone = trim($request->phone);
        $owner->address = $request->address ? trim($request->address) : null;
        $owner->password = Hash::make($request->password);
        $owner->role = 'FSO'; // Explicitly set role to FSO
        $owner->role_type = 'OWNER';
        $owner->status = $request->status ?? 'A';
        $owner->save();

        return redirect()->route('admin.fso.index')
            ->with('success', 'Franchise Sponsorship Owner "' . $owner->name . '" has been created successfully with role FSO.');
    }

    /**
     * Update an existing Franchise Sponsorship Owner
     */
    public function update(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->id,
            'phone' => 'required|string|max:20',
            'address' => 'nullable|string|max:500',
            'password' => 'nullable|string|min:6',
            'status' => 'nullable|in:A,I',
        ], [
            'name.required' => 'Please enter the owner full name.',
            'email.required' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered to another user.',
            'phone.required' => 'Please enter the contact phone number.',
            'password.min' => 'The new password must be at least 6 characters long if provided.',
        ]);

        $owner = User::where('id', $request->id)
            ->where('role', 'FSO')
            ->firstOrFail();

        $owner->name = trim($request->name);
        $owner->email = strtolower(trim($request->email));
        $owner->phone = trim($request->phone);
        $owner->address = $request->address ? trim($request->address) : null;

        if ($request->filled('password')) {
            $owner->password = Hash::make($request->password);
        }

        if ($request->has('status')) {
            $owner->status = $request->status;
        }

        $owner->save();

        return redirect()->route('admin.fso.index')
            ->with('success', 'Franchise Sponsorship Owner details for "' . $owner->name . '" updated successfully.');
    }

    /**
     * Delete a Franchise Sponsorship Owner
     */
    public function delete($id)
    {
        $this->authorizeAdmin();

        $owner = User::where('id', $id)
            ->where('role', 'FSO')
            ->firstOrFail();

        $ownerName = $owner->name;
        $owner->delete();

        return redirect()->route('admin.fso.index')
            ->with('success', 'Franchise Sponsorship Owner "' . $ownerName . '" has been deleted successfully.');
    }

    /**
     * Toggle status between Active and Inactive
     */
    public function toggleStatus($id)
    {
        $this->authorizeAdmin();

        $owner = User::where('id', $id)
            ->where('role', 'FSO')
            ->firstOrFail();

        $currentStatus = strtoupper($owner->status ?? 'A');
        $newStatus = in_array($currentStatus, ['A', 'ACTIVE', '1']) ? 'I' : 'A';
        $owner->status = $newStatus;
        $owner->save();

        $statusText = $newStatus === 'A' ? 'Active' : 'Inactive';

        return redirect()->route('admin.fso.index')
            ->with('success', 'Status for "' . $owner->name . '" changed to ' . $statusText . '.');
    }

    /**
     * Get mapped restaurants and available restaurants for a Franchise Owner (Parent restaurants only)
     */
    public function getMappings($userId)
    {
        $this->authorizeAdmin();

        $owner = User::where('id', $userId)
            ->where('role', 'FSO')
            ->firstOrFail();

        // Only show parent restaurants in mapping list (exclude sub-restaurants/outlets)
        $mappings = FranchiseRestaurantMapping::where('user_id', $owner->id)
            ->whereHas('restaurant', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('parent_id')->orWhere('parent_id', 0);
                })->where('status', '!=', 'D');
            })
            ->with('restaurant')
            ->latest('id')
            ->get()
            ->map(function ($map) {
                $rest = $map->restaurant;
                return [
                    'mapping_id' => $map->id,
                    'restaurant_id' => $rest ? $rest->id : $map->restaurant_id,
                    'name' => $rest ? $rest->name : 'Unknown Restaurant',
                    'restaurant_id_unique' => $rest ? $rest->restaurant_id_unique : null,
                    'address' => $rest ? $rest->address : null,
                    'status' => $rest ? $rest->status : null,
                    'mapped_at' => $map->created_at ? $map->created_at->format('d M Y, h:i A') : null,
                ];
            });

        $mappedRestaurantIds = $mappings->pluck('restaurant_id')->filter()->toArray();

        // Only show parent restaurants in available dropdown (exclude sub-restaurants/outlets)
        $availableRestaurants = RestaurantMaster::where('status', '!=', 'D')
            ->where(function ($q) {
                $q->whereNull('parent_id')->orWhere('parent_id', 0);
            })
            ->whereNotIn('id', $mappedRestaurantIds)
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'restaurant_id_unique', 'address', 'status']);

        return response()->json([
            'success' => true,
            'owner' => [
                'id' => $owner->id,
                'name' => $owner->name,
                'email' => $owner->email,
                'count' => $mappings->count(),
            ],
            'mapped' => $mappings,
            'available' => $availableRestaurants,
        ]);
    }

    /**
     * Attach a restaurant to a Franchise Owner (Parent restaurants only)
     */
    public function attachMapping(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'restaurant_id' => 'required|exists:restaurant_master,id',
        ]);

        $owner = User::where('id', $request->user_id)
            ->where('role', 'FSO')
            ->firstOrFail();

        $restaurant = RestaurantMaster::findOrFail($request->restaurant_id);

        // Validation: reject sub-restaurants/outlets
        if (!empty($restaurant->parent_id) && $restaurant->parent_id > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Only parent restaurants can be mapped. Sub-restaurants or branch outlets of a parent cannot be mapped to franchise owners.',
            ], 422);
        }

        $existing = FranchiseRestaurantMapping::where('user_id', $owner->id)
            ->where('restaurant_id', $restaurant->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant "' . $restaurant->name . '" is already mapped to this franchise owner.',
            ], 422);
        }

        FranchiseRestaurantMapping::create([
            'user_id' => $owner->id,
            'restaurant_id' => $restaurant->id,
        ]);

        $totalMapped = FranchiseRestaurantMapping::where('user_id', $owner->id)
            ->whereHas('restaurant', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('parent_id')->orWhere('parent_id', 0);
                })->where('status', '!=', 'D');
            })
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Restaurant "' . $restaurant->name . '" successfully mapped to ' . $owner->name . '.',
            'total_mapped' => $totalMapped,
        ]);
    }

    /**
     * Detach a restaurant from a Franchise Owner
     */
    public function detachMapping(Request $request)
    {
        $this->authorizeAdmin();

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'restaurant_id' => 'required|exists:restaurant_master,id',
        ]);

        $owner = User::where('id', $request->user_id)
            ->where('role', 'FSO')
            ->firstOrFail();

        $restaurant = RestaurantMaster::find($request->restaurant_id);
        $restName = $restaurant ? $restaurant->name : 'Restaurant';

        $deleted = FranchiseRestaurantMapping::where('user_id', $owner->id)
            ->where('restaurant_id', $request->restaurant_id)
            ->delete();

        $totalMapped = FranchiseRestaurantMapping::where('user_id', $owner->id)
            ->whereHas('restaurant', function ($q) {
                $q->where(function ($sub) {
                    $sub->whereNull('parent_id')->orWhere('parent_id', 0);
                })->where('status', '!=', 'D');
            })
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Restaurant "' . $restName . '" detached successfully.',
            'total_mapped' => $totalMapped,
        ]);
    }
}
