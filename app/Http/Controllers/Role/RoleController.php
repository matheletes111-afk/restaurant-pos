<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RoleToPermission;
class RoleController extends Controller
{
    public function index()
    {
        $data = [];
        $data['data'] = Role::where('status','A')->get();
        return view('role.index',$data);
    }

    public function add()
    {
        return view('role.add');
    }

    public function edit($id)
    {
        $data = [];
        $data['data'] = Role::where('id',$id)->first();
        return view('role.edit',$data);
    }

    public function insert(Request $request)
    {
        $new = new Role;
        $new->title = $request->title;
        $new->description = $request->description;
        $new->created_by = auth()->user()->id;
        $new->save();

        $upd = [];
        return redirect()->route('manage.operations.role.management')->with('success','Role inserted successfully');
    }

    public function update(Request $request)
    {
        $upd = [];
        $upd['title'] = $request->title;
        $upd['description'] = $request->description;
        $upd['created_by'] = auth()->user()->id;
        Role::where('id',$request->id)->update($upd);
        return redirect()->back()->with('success','Role updated successfully');
    }

    public function delete($id)
    {
        Role::where('id',$id)->update(['status'=>'D','deleted_by'=>auth()->user()->id]);
        return redirect()->back()->with('success','Role deleted successfully');
    }

    // public function permissionManage($id)
    // {
    //     $data = [];
    //     $data['selected'] = RoleToPermission::where('role_id',$id)->pluck('menu_id')->toArray();
    //     $data['role_id'] = $id;
    //     return view('role.permission',$data);
    // }

    // public function permissionManageUpdate(Request $request)
    // {
    //         $roleId = $request->input('role_id'); // make sure it's in the form
    //         $permissions = $request->input('permissions', []); // array of permission IDs

    //         // 1. Delete existing permissions for this role
    //         RoleToPermission::where('role_id', $roleId)->delete();

    //         // 2. Insert new permissions
    //         $insertData = [];
    //         foreach ($permissions as $permissionId) {
    //                 $new = new RoleToPermission;
    //                  $new->role_id = $roleId;
    //                  $new->menu_id = $permissionId;
    //                  $new->save();
    //         }

    //         return redirect()->back()->with('success', 'Permissions updated successfully!');
    // }
}
