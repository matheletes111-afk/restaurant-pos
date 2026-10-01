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
        $sheet->setTitle('Products Sample');
        
        // Headers
        $sheet->setCellValue('A1', 'Product Name');
        $sheet->setCellValue('B1', 'Unit Name / ID');
        $sheet->setCellValue('C1', 'Opening Qty');
        
        // Sample data
        $sheet->setCellValue('A2', 'Basmati Rice');
        $sheet->setCellValue('B2', 'Kg');
        $sheet->setCellValue('C2', '50');
        
        $sheet->setCellValue('A3', 'Chicken Breast');
        $sheet->setCellValue('B3', 'Kg');
        $sheet->setCellValue('C3', '25.5');
        
        $sheet->setCellValue('A4', 'Cooking Oil');
        $sheet->setCellValue('B4', 'Liter');
        $sheet->setCellValue('C4', '30');

        $sheet->setCellValue('A5', 'Fresh Paneer');
        $sheet->setCellValue('B5', 'Kg');
        $sheet->setCellValue('C5', '15');

        $sheet->setCellValue('A6', 'Egg Trays');
        $sheet->setCellValue('B6', 'Pcs');
        $sheet->setCellValue('C6', '100');
        
        // Style Header
        $sheet->getStyle('A1:C1')->getFont()->setBold(true)->getColor()->setARGB('FF0F172A');
        $sheet->getStyle('A1:C1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFF1F5F9');
        $sheet->getStyle('A1:C6')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        
        // Auto size columns
        foreach (range('A', 'C') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        // Get existing active units for reference in Column E & F
        $units = Unit::where('restaurant_id', auth()->user()->restaurant_id)
            ->where('status', 'A')
            ->orderBy('name', 'asc')
            ->get();
        
        if ($units->count() > 0) {
            $sheet->setCellValue('E1', 'Configured Units in your Restaurant');
            $sheet->setCellValue('E2', 'Unit Name');
            $sheet->setCellValue('F2', 'Unit ID');
            
            $sheet->getStyle('E1')->getFont()->setBold(true)->getColor()->setARGB('FF047857');
            $sheet->getStyle('E2:F2')->getFont()->setBold(true);
            $sheet->getStyle('E2:F2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFECFDF5');

            $row = 3;
            foreach ($units as $unit) {
                $sheet->setCellValue('E' . $row, $unit->name);
                $sheet->setCellValue('F' . $row, $unit->id);
                $row++;
            }
            $sheet->getColumnDimension('E')->setAutoSize(true);
            $sheet->getColumnDimension('F')->setAutoSize(true);
        }
        
        $writer = new Xlsx($spreadsheet);
        $filename = 'products_bulk_upload_sample.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'nullable|file|mimes:xlsx,xls,csv,txt|max:5120',
            'bulk_file' => 'nullable|file|mimes:xlsx,xls,csv,txt|max:5120'
        ]);

        $file = $request->file('excel_file') ?? $request->file('bulk_file');
        if (!$file) {
            return redirect()->back()->with('error', 'Please select an Excel or CSV file to upload.');
        }

        $restaurantId = auth()->user()->restaurant_id;
        $userId = auth()->user()->id;

        DB::beginTransaction();
        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();
            
            $successCount = 0;
            $skippedCount = 0;
            $errors = [];
            
            if (empty($rows) || count($rows) <= 1) {
                return redirect()->back()->with('error', 'The uploaded file contains no data rows.');
            }

            // Pre-load existing active units for this restaurant
            $existingUnits = Unit::where('restaurant_id', $restaurantId)
                ->where('status', 'A')
                ->get();

            // Skip header row (row index 0)
            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];
                
                // Skip if entire row or first column is empty
                if (!isset($row[0]) || trim((string)$row[0]) === '') {
                    continue;
                }
                
                $productName = trim((string)$row[0]);
                $unitInput = isset($row[1]) ? trim((string)$row[1]) : '';
                $openingQtyRaw = isset($row[2]) ? trim((string)$row[2]) : '0';
                $openingQty = is_numeric($openingQtyRaw) ? (float) $openingQtyRaw : 0;
                
                if (empty($productName)) {
                    continue;
                }
                
                // Check if product already exists
                $existingProduct = Product::where('restaurant_id', $restaurantId)
                    ->where('status', 'A')
                    ->whereRaw('LOWER(product_name) = ?', [strtolower($productName)])
                    ->first();
                
                if ($existingProduct) {
                    $skippedCount++;
                    $errors[] = "Row " . ($i + 1) . ": Product '{$productName}' already exists (skipped).";
                    continue;
                }
                
                // Resolve Unit
                $unitId = null;
                if (!empty($unitInput)) {
                    if (is_numeric($unitInput)) {
                        $matchedUnit = $existingUnits->firstWhere('id', (int)$unitInput);
                    } else {
                        $matchedUnit = $existingUnits->first(function ($u) use ($unitInput) {
                            return strcasecmp($u->name, $unitInput) === 0;
                        });
                    }

                    // If unit does not exist, auto-create it
                    if (!$matchedUnit) {
                        $newUnitName = is_numeric($unitInput) ? 'Unit ' . $unitInput : $unitInput;
                        $createdUnit = Unit::create([
                            'name' => $newUnitName,
                            'status' => 'A',
                            'restaurant_id' => $restaurantId,
                            'created_by' => auth()->user()->name ?? 'System'
                        ]);
                        $existingUnits->push($createdUnit);
                        $unitId = $createdUnit->id;
                    } else {
                        $unitId = $matchedUnit->id;
                    }
                }
                
                // Create product
                $product = new Product();
                $product->product_name = $productName;
                $product->unit_id = $unitId;
                $product->opening_qty = $openingQty;
                $product->restaurant_id = $restaurantId;
                $product->user_id = $userId;
                $product->status = 'A';
                $product->save();
                
                // Create or update inventory record if opening quantity > 0
                if ($openingQty > 0) {
                    $inventory = Inventory::firstOrNew([
                        'product_id' => $product->id,
                        'restaurant_id' => $restaurantId
                    ]);
                    $inventory->total_qty = $openingQty;
                    $inventory->opening_qty = $openingQty;
                    $inventory->created_by = auth()->user()->name ?? 'System';
                    $inventory->save();
                }
                
                $successCount++;
            }
            
            DB::commit();
            
            if ($successCount === 0 && $skippedCount > 0) {
                return redirect()->route('products.manage')
                    ->with('warning', "No new products added. {$skippedCount} products already exist.")
                    ->with('import_errors', $errors);
            }

            $message = "Bulk upload completed! Successfully added {$successCount} product(s).";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} duplicate items skipped).";
            }

            return redirect()->route('products.manage')
                ->with('success', $message)
                ->with('import_errors', !empty($errors) ? $errors : null);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Bulk upload failed: ' . $e->getMessage());
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