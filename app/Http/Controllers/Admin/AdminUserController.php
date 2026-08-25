<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function index()
    {
        if (auth()->user()->role != 'SA') {
            abort(403, 'Unauthorized access.');
        }

        // Fetch super admin users except the main super admin (ID 1)
        $users = User::where('role', 'SA')
            ->where('id', '!=', 1)
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role != 'SA') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6',
            'permissions' => 'nullable|array'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->password = bcrypt($request->password);
        $user->role = 'SA';
        $user->status = 'Active';
        $user->permissions = $request->permissions ?? [];
        $user->save();

        return redirect()->back()->with('success', 'Admin user created successfully.');
    }

    public function update(Request $request)
    {
        if (auth()->user()->role != 'SA') {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6',
            'permissions' => 'nullable|array'
        ]);

        $user = User::find($request->id);
        if ($user->id == 1) {
            return redirect()->back()->with('error', 'Cannot update default admin user.');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->permissions = $request->permissions ?? [];
        $user->save();

        return redirect()->back()->with('success', 'Admin user updated successfully.');
    }

    public function delete($id)
    {
        if (auth()->user()->role != 'SA') {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($id);
        if ($user->id == 1) {
            return redirect()->back()->with('error', 'Cannot delete default admin user.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Admin user deleted successfully.');
    }
}
