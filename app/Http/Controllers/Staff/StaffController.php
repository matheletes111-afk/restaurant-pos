<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Mail\Register;
use Mail;
class StaffController extends Controller
{
    public function index()
    {
        $data = [];
        $data['data'] = User::where('role','STAFF')->where('status','!=','D')->get();
        $data['role'] = Role::where('status','!=','D')->get();
        return view('staff.index',$data);
    }

    public function insert(Request $request)
    {
        $emailcheck = User::where('email',$request->email)->first();
        if (@$emailcheck!="") {
            return redirect()->back()->with('error','Email already exists.Try another one.');
        }

        $new = new User;
        $new->name = $request->name;
        $new->email = $request->email;
        $new->phone = $request->phone;
        $new->role_id = $request->role_id;
        $new->role = 'STAFF';
        $new->address = $request->address;
        $new->status = $request->status;
        $new->updated_by = auth()->user()->id;
        $new->password = \Hash::make($request->password);
        $new->save();

        // Prepare mail data
        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => $request->password,
        ];

        // Try sending mail but don't stop if fails
        try {
            Mail::send(new Register($data));
        } catch (\Exception $e) {
            \Log::error('Driver Registration Mail Failed: '.$e->getMessage());
            // You can also notify admin if needed, but no error shown to user
        }
        return redirect()->back()->with('success', 'Staff details saved successfully!');
    }

    public function update(Request $request)
    {
        $emailCheck = User::where('email', $request->email)
                      ->where('id', '!=', $request->id)
                      ->first();
        if ($emailCheck) {
            return redirect()->back()->with('error', 'Email already exists. Try another one.');
        }

        $staff = User::findOrFail($request->id);

        $staff->name     = $request->name;
        $staff->email    = $request->email;
        $staff->phone    = $request->phone;
        $staff->role_id  = $request->role_id;
        $staff->address  = $request->address;
        $staff->status   = $request->status;
        $staff->updated_by = auth()->user()->id;
        $staff->save();
        return redirect()->back()->with('success', 'Staff details updated successfully!');
    }

    public function delete($id)
    {
        User::where('id',$id)->update(['status'=>'D','updated_by'=>auth()->user()->id]);
        return redirect()->back()->with('success', 'Staff details deleted successfully!');
    }
}
