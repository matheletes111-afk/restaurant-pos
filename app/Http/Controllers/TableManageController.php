<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableManage;
use App\Models\Subscription;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\GdImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use App\Models\Plan;
class TableManageController extends Controller
{
    public function index()
    {
        $data['tables'] = TableManage::where('status', '!=', 'D')->where('restaurant_id',auth()->user()->restaurant_id)->get();
        $check_plan = Subscription::where('user_id',auth()->user()->restaurant_id)->where('status','active')->first();
        $data['plan_details'] = Plan::where('id',$check_plan->plan_id)->first();
        return view('restaurant.table', $data);
    }

public function store(Request $request)
{
    // SAVE TABLE DETAILS
    $table = new TableManage();
    $table->name = $request->name;
    $table->description = $request->description;
    $table->user_id = auth()->user()->id;
    $table->restaurant_id = auth()->user()->restaurant_id;
    $table->status = 'A';
    $table->save();

    // QR LINK
    $qrLink = url('/restaurant/table/'.$table->id.'/'.$table->restaurant_id);

    // QR FILE NAME
    $fileName = 'qr_'.$table->id.'.png';
    $qrPath  = public_path('qrcodes/'.$fileName);

    // Generate using GD
    \QrCode::format('png')
        ->size(300)
        ->generate($qrLink, $qrPath);

    // SAVE QR NAME
    $table->qr_code = $fileName;
    $table->save();

    return redirect()->back()->with('success', 'Table added successfully with QR Code.');
}



    public function update(Request $request)
    {
        $table = TableManage::find($request->id);
        $table->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Table updated successfully.');
    }

    public function status($id)
    {
        $table = TableManage::find($id);
        if ($table) {
            $table->status = $table->status === 'A' ? 'I' : 'A';
            $table->save();
            return redirect()->back()->with('success', 'Status updated successfully.');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }

    public function delete($id)
    {
        $table = TableManage::find($id);
        if ($table) {
            $table->status = 'D';
            $table->save();
            return redirect()->back()->with('success', 'Table deleted successfully.');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }
}
