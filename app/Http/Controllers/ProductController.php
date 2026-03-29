<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Inventory;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['unit', 'inventory'])
            ->where('status', 'A')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('id', 'desc')
            ->get();
        
        $units = Unit::where('status', 'A')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->orderBy('name', 'asc')
            ->get();
        
        return view('products', compact('products', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'unit_id' => 'nullable|exists:units,id',
            'opening_qty' => 'nullable|numeric|min:0'
        ]);

        // Check for duplicate product
        $check = Product::where('product_name', $request->product_name)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->first();
        
        if ($check) {
            return redirect()->back()->with('error', 'Product already exists!');
        }

        DB::beginTransaction();
        try {
            // Create product
            $product = new Product();
            $product->product_name = $request->product_name;
            $product->unit_id = $request->unit_id;
            $product->opening_qty = $request->opening_qty ?? 0;
            $product->restaurant_id = auth()->user()->restaurant_id;
            $product->user_id = auth()->user()->id;
            $product->status = 'A';
            $product->save();

            // Create inventory record
            if ($request->opening_qty > 0) {
                $inventory = new Inventory();
                $inventory->product_id = $product->id;
                $inventory->total_qty = $request->opening_qty;
                $inventory->opening_qty = $request->opening_qty;
                $inventory->created_by = auth()->user()->name;
                $inventory->restaurant_id = auth()->user()->restaurant_id;
                $inventory->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Product added successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to add product: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:products,id',
            'product_name' => 'required|string|max:255|unique:products,product_name,' . $request->id . ',id,restaurant_id,' . auth()->user()->restaurant_id,
            'unit_id' => 'nullable|exists:units,id',
            'opening_qty' => 'nullable|numeric|min:0'
        ]);

        DB::beginTransaction();
        try {
            $product = Product::findOrFail($request->id);
            $product->product_name = $request->product_name;
            $product->unit_id = $request->unit_id;
            
            // Update opening quantity and inventory
            if ($request->opening_qty != $product->opening_qty) {
                $product->opening_qty = $request->opening_qty ?? 0;
                
                // Update or create inventory
                $inventory = Inventory::firstOrNew([
                    'product_id' => $product->id,
                    'restaurant_id' => auth()->user()->restaurant_id
                ]);
                $inventory->total_qty = $request->opening_qty;
                $inventory->opening_qty = $request->opening_qty;
                $inventory->created_by = auth()->user()->name;
                $inventory->restaurant_id = auth()->user()->restaurant_id;
                $inventory->save();
            }
            
            $product->save();
            
            DB::commit();
            return redirect()->back()->with('success', 'Product updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        DB::beginTransaction();
        try {
            $product = Product::findOrFail($id);
            $product->status = 'D';
            $product->save();

            // Soft delete inventory
            $inventory = Inventory::where('product_id', $id)
                ->where('restaurant_id', auth()->user()->restaurant_id)
                ->first();
            
            if ($inventory) {
                $inventory->delete();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Product deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    // Excel Import Views
    public function importView()
    {
        return view('products-import');
    }

    public function downloadSample()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'Product Name');
        $sheet->setCellValue('B1', 'Unit ID');
        $sheet->setCellValue('C1', 'Opening Qty');
        $sheet->setCellValue('D1', 'Notes');
        
        // Sample data
        $sheet->setCellValue('A2', 'Chicken Breast');
        $sheet->setCellValue('B2', '1');
        $sheet->setCellValue('C2', '50');
        $sheet->setCellValue('D2', 'Fresh chicken breast');
        
        $sheet->setCellValue('A3', 'Rice');
        $sheet->setCellValue('B3', '2');
        $sheet->setCellValue('C3', '100');
        $sheet->setCellValue('D3', 'Basmati rice');
        
        // Auto size columns
        foreach (range('A', 'D') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Get units for reference
        $units = Unit::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->get();
        
        if ($units->count() > 0) {
            $sheet->setCellValue('F1', 'Unit Reference');
            $sheet->setCellValue('F2', 'ID - Unit Name');
            
            $row = 3;
            foreach ($units as $unit) {
                $sheet->setCellValue('F' . $row, $unit->id . ' - ' . $unit->name);
                $row++;
            }
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'products_import_sample.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        DB::beginTransaction();
        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $successCount = 0;
            $errorCount = 0;
            $errors = [];
            
            // Skip header row (row 1)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip empty rows
                if (empty($row[0])) {
                    continue;
                }
                
                $productName = trim($row[0]);
                $unitId = isset($row[1]) ? trim($row[1]) : null;
                $openingQty = isset($row[2]) ? (float) trim($row[2]) : 0;
                
                // Validate row data
                if (empty($productName)) {
                    $errors[] = "Row " . ($i + 1) . ": Product name is required";
                    $errorCount++;
                    continue;
                }
                
                // Check if product already exists
                $existingProduct = Product::where('product_name', $productName)
                    ->where('restaurant_id', auth()->user()->restaurant_id)
                    ->where('status', 'A')
                    ->first();
                
                if ($existingProduct) {
                    $errors[] = "Row " . ($i + 1) . ": Product '{$productName}' already exists";
                    $errorCount++;
                    continue;
                }
                
                // Validate unit if provided
                if ($unitId) {
                    $unit = Unit::where('id', $unitId)
                        ->where('restaurant_id', auth()->user()->restaurant_id)
                        ->where('status', 'A')
                        ->first();
                    
                    if (!$unit) {
                        $errors[] = "Row " . ($i + 1) . ": Unit ID '{$unitId}' is invalid";
                        $errorCount++;
                        continue;
                    }
                }
                
                // Create product
                $product = new Product();
                $product->product_name = $productName;
                $product->unit_id = $unitId;
                $product->opening_qty = $openingQty;
                $product->restaurant_id = auth()->user()->restaurant_id;
                $product->user_id = auth()->user()->id;
                $product->status = 'A';
                $product->save();
                
                // Create inventory record if opening quantity > 0
                if ($openingQty > 0) {
                    $inventory = new Inventory();
                    $inventory->product_id = $product->id;
                    $inventory->total_qty = $openingQty;
                    $inventory->opening_qty = $openingQty;
                    $inventory->created_by = auth()->user()->name;
                    $inventory->restaurant_id = auth()->user()->restaurant_id;
                    $inventory->save();
                }
                
                $successCount++;
            }
            
            DB::commit();
            
            $message = "Import completed: {$successCount} products imported successfully.";
            if ($errorCount > 0) {
                $message .= " {$errorCount} errors found.";
                if (!empty($errors)) {
                    session()->flash('import_errors', $errors);
                }
            }
            
            return redirect()->route('products.manage')->with(
                $errorCount > 0 ? 'warning' : 'success',
                $message
            );
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $products = Product::with(['unit', 'inventory'])
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('id', 'desc')
            ->get();
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Product Name');
        $sheet->setCellValue('C1', 'Unit');
        $sheet->setCellValue('D1', 'Opening Qty');
        $sheet->setCellValue('E1', 'Current Stock');
        $sheet->setCellValue('F1', 'Created At');
        
        // Data
        $row = 2;
        foreach ($products as $product) {
            $sheet->setCellValue('A' . $row, $product->id);
            $sheet->setCellValue('B' . $row, $product->product_name);
            $sheet->setCellValue('C' . $row, $product->unit ? $product->unit->name : 'N/A');
            $sheet->setCellValue('D' . $row, $product->opening_qty);
            $sheet->setCellValue('E' . $row, $product->inventory ? $product->inventory->total_qty : 0);
            $sheet->setCellValue('F' . $row, $product->created_at->format('Y-m-d H:i:s'));
            $row++;
        }
        
        // Auto size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Style header
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'products_export_' . date('Y_m_d_His') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
}