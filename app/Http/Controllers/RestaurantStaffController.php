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
}
