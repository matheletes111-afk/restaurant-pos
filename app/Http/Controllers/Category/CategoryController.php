<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Plan;
use App\Models\SubCategory;
use App\Models\Subscription;
use App\Models\Exam;
use Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class CategoryController extends Controller
{
    public function index()
    {
        $data = [];
        $data['data'] = Category::where('status','!=','D')->where('restaurant_id',auth()->user()->restaurant_id)->get();
        $check_plan = Subscription::where('user_id',auth()->user()->restaurant_id)->where('status','active')->first();
        $data['plan_details'] = Plan::where('id',$check_plan->plan_id)->first();
        return view('category_admin',$data);
    }

    public function insert(Request $request)
    {
        $new = new Category;
        $new->name = $request->name;
        $new->user_id = auth()->user()->id;
        $new->restaurant_id = auth()->user()->restaurant_id;
        if ($request->image) {
            $image = $request->image;
            $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            //real image
            $image->move("storage/app/public/category",$filename);    
            $new->image = $filename;
        }
        $new->save();
        $upd = [];
        $upd['slug'] = Str::slug($request->name).'-'.$new->id;
        Category::where('id',$new->id)->update($upd);
        return redirect()->back()->with('success','Category inserted successfully');
    }

    public function update(Request $request)
    {
        $upd = [];
        $upd['name'] = $request->name;
        $upd['slug'] = Str::slug($request->name).'-'.$request->id;
        if ($request->image) {
            $check = Category::where('id',$request->id)->first();
            @unlink('storage/app/public/category/'.$check->image);
            $image = $request->image;
            $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            //real image
            $image->move("storage/app/public/category",$filename);    
            $upd['image'] = $filename;
        }
        Category::where('id',$request->id)->update($upd);
        return redirect()->back()->with('success','Category updated successfully');
    }

    public function delete($id)
    {
        $check = Category::where('id',$id)->where('restaurant_id',auth()->user()->restaurant_id)->first();
        @unlink('storage/app/public/category/'.$check->image);
        Category::where('id',$id)->update(['status'=>'D']);
        return redirect()->back()->with('success','Category deleted successfully');
    }

    public function subCategory($id)
    {
        $data = [];
        $data['data'] = SubCategory::where('category_id',$id)->where('restaurant_id',auth()->user()->restaurant_id)->where('status','!=','D')->get();
        $data['details'] = Category::where('id',$id)->where('restaurant_id',auth()->user()->restaurant_id)->first();
         if ($data['details']=="") {
           return redirect()->back()->with('error','Unauthorized Access');
        }
        $check_plan = Subscription::where('user_id',auth()->user()->restaurant_id)->where('status','active')->first();
        $data['plan_details'] = Plan::where('id',$check_plan->plan_id)->first();
        $data['id'] = $id;
        return view('sub_index',$data);
    }

    public function subCategoryinsert(Request $request)
    {
        $new = new SubCategory;
        $new->name = $request->name;
        $new->price = $request->price;
        $new->gst_rate = $request->gst_rate;
        $new->food_type = $request->food_type;
        $new->category_id = $request->category_id;
        $new->user_id = auth()->user()->id;
        $new->restaurant_id = auth()->user()->restaurant_id;
        if ($request->image) {
            $image = $request->image;
            $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            //real image
            $image->move("storage/app/public/category",$filename);    
            $new->image = $filename;
        }
        $new->save();
        return redirect()->back()->with('success','Product inserted successfully');
    }

    public function subCategoryupdate(Request $request)
    {
        $upd = [];
        $upd['name'] = $request->name;
        $upd['price'] = $request->price;
        $upd['gst_rate'] = $request->gst_rate;
        $upd['food_type'] = $request->food_type;
        if ($request->image) {
            $check = Category::where('id',$request->id)->first();
            @unlink('storage/app/public/category/'.$check->image);
            $image = $request->image;
            $filename = time() . '-' . rand(1000, 9999) . '.' . $image->getClientOriginalExtension();
            //real image
            $image->move("storage/app/public/category",$filename);    
            $upd['image'] = $filename;
        }
        SubCategory::where('id',$request->id)->update($upd);
        return redirect()->back()->with('success','Product updated successfully');
    }

    public function subCategorydelete($id)
    {
        $check = SubCategory::where('id',$id)->where('restaurant_id',auth()->user()->restaurant_id)->first();
        @unlink('storage/app/public/category/'.$check->image);
        SubCategory::where('id',$id)->where('restaurant_id',auth()->user()->restaurant_id)->update(['status'=>'D']);
        return redirect()->back()->with('success','Product deleted successfully');
    }

    public function subCategorystatus($id)
    {
        $product = SubCategory::find($id);
        if ($product) {
            $product->status = $product->status === 'A' ? 'I' : 'A';
            $product->save();
            return redirect()->back()->with('success', 'Status updated successfully.');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }

    public function bulkUpload(Request $request)
{
    $request->validate([
        
        'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
    ]);

    $categoryId = $request->category_id;
    
    try {
        $file = $request->file('bulk_file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // Skip header row (if exists)
        $headerSkipped = false;
        
        foreach ($rows as $index => $row) {
            // Skip empty rows
            if (empty(array_filter($row))) {
                continue;
            }
            
            // Skip header row (first row with column names)
            if (!$headerSkipped && is_string($row[0]) && strtolower($row[0]) == 'product name') {
                $headerSkipped = true;
                continue;
            }
            
            // Validate required columns
            if (count($row) < 4) {
                $errors[] = "Row " . ($index + 1) . ": Insufficient data";
                $errorCount++;
                continue;
            }
            
            // Extract data from columns
            $name = trim($row[0] ?? '');
            $price = $row[1] ?? 0;
            $gstRate = $row[2] ?? 0;
            $foodType = strtoupper(trim($row[3] ?? 'VEG'));
            
            // Validate data
            if (empty($name)) {
                $errors[] = "Row " . ($index + 1) . ": Product name is required";
                $errorCount++;
                continue;
            }
            
            if (!is_numeric($price) || $price < 0) {
                $errors[] = "Row " . ($index + 1) . ": Invalid price";
                $errorCount++;
                continue;
            }
            
            if (!is_numeric($gstRate) || $gstRate < 0) {
                $errors[] = "Row " . ($index + 1) . ": Invalid GST rate";
                $errorCount++;
                continue;
            }
            
            if (!in_array($foodType, ['VEG', 'NON-VEG'])) {
                $errors[] = "Row " . ($index + 1) . ": Food type must be VEG or NON-VEG";
                $errorCount++;
                continue;
            }
            
            try {
                $subCategory = new SubCategory();
                $subCategory->name = $name;
                $subCategory->price = $price;
                $subCategory->gst_rate = $gstRate;
                $subCategory->food_type = $foodType;
                $subCategory->category_id = $categoryId;
                $subCategory->user_id = auth()->user()->id;
                $subCategory->restaurant_id = auth()->user()->restaurant_id;
                $subCategory->status = 'A';
                $subCategory->save();
                
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                $errorCount++;
            }
        }
        
        $message = "Bulk upload completed. Success: {$successCount}, Failed: {$errorCount}";
        
        if (!empty($errors)) {
            $message .= "<br>Errors:<br>" . implode("<br>", $errors);
            return redirect()->back()->with('warning', $message);
        }
        
        return redirect()->back()->with('success', $message);
        
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Error processing file: ' . $e->getMessage());
    }
}

public function downloadTemplate($id)
{
    // Create new Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Set headers
    $sheet->setCellValue('A1', 'Product Name');
    $sheet->setCellValue('B1', 'Price');
    $sheet->setCellValue('C1', 'GST Rate (%)');
    $sheet->setCellValue('D1', 'Food Type (VEG/NON-VEG)');
    
    // Add sample data
    $sheet->setCellValue('A2', 'Paneer Butter Masala');
    $sheet->setCellValue('B2', 250);
    $sheet->setCellValue('C2', 18);
    $sheet->setCellValue('D2', 'VEG');
    
    $sheet->setCellValue('A3', 'Chicken Biryani');
    $sheet->setCellValue('B3', 320);
    $sheet->setCellValue('C3', 12);
    $sheet->setCellValue('D3', 'NON-VEG');
    
    // Style the header
    $headerStyle = [
        'font' => ['bold' => true],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FFE0E0E0']
        ]
    ];
    
    $sheet->getStyle('A1:D1')->applyFromArray($headerStyle);
    
    // Auto size columns
    foreach (range('A', 'D') as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }
    
    // Create writer
    $writer = new Xlsx($spreadsheet);
    
    // Set headers for download
    $filename = "product_bulk_upload_template.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer->save('php://output');
    exit;
}


    

}
