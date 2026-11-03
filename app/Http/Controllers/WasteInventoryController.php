<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WasteInventory;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Inventory;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class WasteInventoryController extends Controller
{
    /**
     * Constructor - Check inventory permission for all methods
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            // Check if user is authenticated
            if (!auth()->check()) {
                return redirect()->route('login')
                    ->with('error', 'Please login to continue.');
            }

            // Get active subscription using restaurant_id
            $subRestId = method_exists(auth()->user(), 'getSubscriptionRestaurantId') 
                ? auth()->user()->getSubscriptionRestaurantId() 
                : auth()->user()->restaurant_id;

            $active = DB::table('subscriptions')
                ->where('user_id', $subRestId)
                ->whereIn('status', ['active', 'completed'])
                ->latest('id')
                ->first();

            // If no active subscription found
            if (!$active) {
                return redirect()->back()
                    ->with('error', 'No active subscription found. Please subscribe to a plan.');
            }

            // Get plan details
            $plan_details = DB::table('plans')
                ->where('id', $active->plan_id)
                ->first();

            // Check if inventory_checkbox is NOT "Y"
            if (@$plan_details->inventory_checkbox != "Y") {
                return redirect()->back()
                    ->with('error', 'Unauthorized access. Your plan does not include inventory management features.');
            }

            // Permission granted, continue to the requested method
            return $next($request);
        });
    }

    /**
     * Display Waste Inventory listing with date filters and search
     */
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $isBarEnabled = auth()->check() ? auth()->user()->hasBarAccess() : false;

        // Ensure bar items are synchronized into products with ML unit if bar enabled
        if ($isBarEnabled) {
            Product::syncBarItems($restaurantId);
        }

        $hasExplicitFilter = $request->has('from_date') || $request->has('to_date') || $request->has('keyword') || $request->has('item_type');

        $fromDate = $request->filled('from_date') 
            ? Carbon::parse($request->from_date)->startOfDay() 
            : Carbon::now()->startOfMonth()->startOfDay();

        $toDate = $request->filled('to_date') 
            ? Carbon::parse($request->to_date)->endOfDay() 
            : Carbon::now()->endOfMonth()->endOfDay();

        $itemType = $request->item_type;
        $keyword = $request->keyword;

        $query = WasteInventory::with(['product.unit', 'barItem', 'unit', 'user'])
            ->where('restaurant_id', $restaurantId);

        // Date range filter
        if ($hasExplicitFilter) {
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->where(function($dq) use ($fromDate, $toDate) {
                    $dq->whereBetween('waste_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                       ->orWhereBetween('created_at', [$fromDate, $toDate]);
                });
            } elseif ($request->filled('from_date')) {
                $query->where(function($dq) use ($fromDate) {
                    $dq->where('waste_date', '>=', $fromDate->format('Y-m-d'))
                       ->orWhere('created_at', '>=', $fromDate);
                });
            } elseif ($request->filled('to_date')) {
                $query->where(function($dq) use ($toDate) {
                    $dq->where('waste_date', '<=', $toDate->format('Y-m-d'))
                       ->orWhere('created_at', '<=', $toDate);
                });
            }
        } else {
            // Default initial load:
            // Check if waste records exist in current month
            $currentMonthCount = (clone $query)->where(function($dq) use ($fromDate, $toDate) {
                $dq->whereBetween('waste_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                   ->orWhereBetween('created_at', [$fromDate, $toDate]);
            })->count();

            if ($currentMonthCount > 0) {
                $query->where(function($dq) use ($fromDate, $toDate) {
                    $dq->whereBetween('waste_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                       ->orWhereBetween('created_at', [$fromDate, $toDate]);
                });
            } else {
                // If no records in current month, show all existing records
                $minDate = WasteInventory::where('restaurant_id', $restaurantId)->min('waste_date');
                if ($minDate) {
                    $fromDate = Carbon::parse($minDate)->startOfDay();
                } else {
                    $minCreated = WasteInventory::where('restaurant_id', $restaurantId)->min('created_at');
                    if ($minCreated) {
                        $fromDate = Carbon::parse($minCreated)->startOfDay();
                    }
                }
            }
        }

        // Filter by item type (FOOD / BAR)
        if ($request->filled('item_type') && in_array($request->item_type, ['FOOD', 'BAR'])) {
            $query->where('item_type', $request->item_type);
        }

        // Keyword search
        if ($request->filled('keyword')) {
            $searchTerm = trim($request->keyword);
            $query->where(function($q) use ($searchTerm) {
                $q->where('waste_no', 'like', "%{$searchTerm}%")
                  ->orWhere('reason', 'like', "%{$searchTerm}%")
                  ->orWhereHas('product', function($pq) use ($searchTerm) {
                      $pq->where('product_name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('user', function($uq) use ($searchTerm) {
                      $uq->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $wasteList = $query->orderBy('waste_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Active Food products (Kitchen / Store items)
        $foodProducts = Product::with('unit')
            ->where('restaurant_id', $restaurantId)
            ->whereNull('sub_category_id')
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();

        // Active Bar items
        $barProducts = Product::with(['unit', 'barItem.brand', 'barItem.category'])
            ->where('restaurant_id', $restaurantId)
            ->whereNotNull('sub_category_id')
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();

        $stats = [
            'total_entries'    => $wasteList->count(),
            'food_records'     => $wasteList->where('item_type', 'FOOD')->count(),
            'bar_records'      => $wasteList->where('item_type', 'BAR')->count(),
            'food_waste_qty'   => $wasteList->where('item_type', 'FOOD')->sum('waste_qty'),
            'bar_waste_ml'     => $wasteList->where('item_type', 'BAR')->sum('waste_qty'),
        ];

        $nextWasteNo = WasteInventory::generateWasteNo($restaurantId);

        return view('waste-inventory.index', compact('isBarEnabled', 
            'wasteList',
            'foodProducts',
            'barProducts',
            'fromDate',
            'toDate',
            'itemType',
            'keyword',
            'hasExplicitFilter',
            'stats',
            'nextWasteNo'
        ));
    }

    /**
     * Show dedicated form to record a new Waste Inventory entry
     */
    public function create()
    {
        $restaurantId = auth()->user()->restaurant_id;
        $isBarEnabled = auth()->check() ? auth()->user()->hasBarAccess() : false;

        if ($isBarEnabled) {
            // Ensure bar items are synchronized
            Product::syncBarItems($restaurantId);

            $barProducts = Product::with(['unit', 'barItem.brand', 'barItem.category'])
                ->where('restaurant_id', $restaurantId)
                ->whereNotNull('sub_category_id')
                ->where('status', 'A')
                ->orderBy('product_name')
                ->get();
        } else {
            $barProducts = collect();
        }

        $foodProducts = Product::with('unit')
            ->where('restaurant_id', $restaurantId)
            ->whereNull('sub_category_id')
            ->where('status', 'A')
            ->orderBy('product_name')
            ->get();

        $nextWasteNo = WasteInventory::generateWasteNo($restaurantId);

        return view('waste-inventory.create', compact('foodProducts', 'barProducts', 'nextWasteNo', 'isBarEnabled'));
    }

    /**
     * Store Waste Inventory entry and deduct stock from restaurant inventory
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_type'   => 'required|in:FOOD,BAR',
            'product_id'  => 'required|exists:products,id',
            'waste_qty'   => 'required|numeric|min:0.01',
            'waste_date'  => 'required|date',
            'reason'      => 'nullable|string|max:1000',
        ], [
            'item_type.required'  => 'Please select the Item Type (Food Item or Bar Item).',
            'product_id.required' => 'Please select a product.',
            'waste_qty.required'  => 'Please enter the waste quantity.',
            'waste_qty.min'       => 'Waste quantity must be at least 0.01.',
            'waste_date.required' => 'Please select the waste date.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        DB::beginTransaction();
        try {
            $product = Product::with(['unit', 'barItem'])
                ->where('id', $request->product_id)
                ->where('restaurant_id', $restaurantId)
                ->firstOrFail();

            $isBar = ($request->item_type === 'BAR') || !empty($product->sub_category_id);
            $subCategoryId = $isBar ? $product->sub_category_id : null;
            $unitId = $product->unit_id;

            // Get current stock before deduction for historical tracking
            $currentStock = Inventory::getStock($product->id);

            // Generate unique Waste Number
            $wasteNo = WasteInventory::generateWasteNo($restaurantId);

            // Create Waste Inventory record
            $waste = new WasteInventory();
            $waste->waste_no = $wasteNo;
            $waste->waste_date = $request->waste_date;
            $waste->item_type = $isBar ? 'BAR' : 'FOOD';
            $waste->product_id = $product->id;
            $waste->sub_category_id = $subCategoryId;
            $waste->unit_id = $unitId;
            $waste->waste_qty = $request->waste_qty;
            $waste->current_stock_before = $currentStock;
            $waste->reason = $request->reason ? trim($request->reason) : null;
            $waste->status = 'COMPLETED';
            $waste->restaurant_id = $restaurantId;
            $waste->user_id = auth()->id();
            $waste->save();

            // Deduct quantity from inventory (automatically handles Bar Item sub_category.current_stock_ml in ML)
            Inventory::updateStock($product->id, $request->waste_qty, 'subtract');

            DB::commit();

            $unitName = $isBar ? 'ML' : ($product->unit->name ?? '');
            return redirect()->route('waste-inventory.index')
                ->with('success', "Waste of {$request->waste_qty} {$unitName} for \"{$product->product_name}\" recorded and deducted from inventory successfully! (Ref: {$wasteNo})");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to record waste inventory: ' . $e->getMessage());
        }
    }

    /**
     * Delete Waste record and restore the deducted inventory
     */
    public function delete($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        DB::beginTransaction();
        try {
            $waste = WasteInventory::with('product')
                ->where('id', $id)
                ->where('restaurant_id', $restaurantId)
                ->firstOrFail();

            // Reverse inventory deduction (restore stock)
            Inventory::updateStock($waste->product_id, $waste->waste_qty, 'add');

            $productName = $waste->product->product_name ?? 'Product';
            $qty = $waste->waste_qty;

            // Delete the waste entry
            $waste->delete();

            DB::commit();

            return redirect()->route('waste-inventory.index')
                ->with('success', "Waste entry #{$waste->waste_no} deleted and {$qty} stock restored for \"{$productName}\".");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete waste record: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint: Get product details (unit, current stock, bottle size if bar item)
     */
    public function getProduct($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $product = Product::with(['unit', 'barItem'])
            ->where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found']);
        }

        $isBar = !empty($product->sub_category_id);
        $currentStock = Inventory::getStock($product->id);
        $unitName = $isBar ? 'ML' : ($product->unit->name ?? 'N/A');
        $bottleSize = $isBar ? floatval($product->barItem->bottle_size_ml ?? 750) : null;
        $bottleCount = ($isBar && $bottleSize > 0) ? round($currentStock / $bottleSize, 1) : null;

        return response()->json([
            'success'          => true,
            'product'          => $product,
            'is_bar'           => $isBar,
            'unit_id'          => $product->unit_id,
            'unit_name'        => $unitName,
            'current_stock'    => $currentStock,
            'bottle_size_ml'   => $bottleSize,
            'bottle_count'     => $bottleCount,
        ]);
    }

    /**
     * AJAX endpoint: Check live stock for a product
     */
    public function checkStock($productId)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $product = Product::with(['unit', 'barItem'])
            ->where('id', $productId)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if (!$product) {
            return response()->json(['stock' => 0]);
        }

        $stock = Inventory::getStock($productId);
        $isBar = !empty($product->sub_category_id);

        return response()->json([
            'stock'          => $stock,
            'is_bar'         => $isBar,
            'unit_name'      => $isBar ? 'ML' : ($product->unit->name ?? ''),
            'bottle_size_ml' => $isBar ? floatval($product->barItem->bottle_size_ml ?? 750) : null,
        ]);
    }

    /**
     * Export waste inventory records to Excel (.xlsx)
     */
    public function exportExcel(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $hasExplicitFilter = $request->has('from_date') || $request->has('to_date') || $request->has('keyword') || $request->has('item_type');

        $fromDate = $request->filled('from_date') 
            ? Carbon::parse($request->from_date)->startOfDay() 
            : Carbon::now()->startOfMonth()->startOfDay();

        $toDate = $request->filled('to_date') 
            ? Carbon::parse($request->to_date)->endOfDay() 
            : Carbon::now()->endOfMonth()->endOfDay();

        $query = WasteInventory::with(['product.unit', 'barItem', 'unit', 'user'])
            ->where('restaurant_id', $restaurantId);

        if ($hasExplicitFilter) {
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $query->where(function($dq) use ($fromDate, $toDate) {
                    $dq->whereBetween('waste_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                       ->orWhereBetween('created_at', [$fromDate, $toDate]);
                });
            } elseif ($request->filled('from_date')) {
                $query->where(function($dq) use ($fromDate) {
                    $dq->where('waste_date', '>=', $fromDate->format('Y-m-d'))
                       ->orWhere('created_at', '>=', $fromDate);
                });
            } elseif ($request->filled('to_date')) {
                $query->where(function($dq) use ($toDate) {
                    $dq->where('waste_date', '<=', $toDate->format('Y-m-d'))
                       ->orWhere('created_at', '<=', $toDate);
                });
            }
        } else {
            $query->where(function($dq) use ($fromDate, $toDate) {
                $dq->whereBetween('waste_date', [$fromDate->format('Y-m-d'), $toDate->format('Y-m-d')])
                   ->orWhereBetween('created_at', [$fromDate, $toDate]);
            });
        }

        if ($request->filled('item_type') && in_array($request->item_type, ['FOOD', 'BAR'])) {
            $query->where('item_type', $request->item_type);
        }

        if ($request->filled('keyword')) {
            $searchTerm = trim($request->keyword);
            $query->where(function($q) use ($searchTerm) {
                $q->where('waste_no', 'like', "%{$searchTerm}%")
                  ->orWhere('reason', 'like', "%{$searchTerm}%")
                  ->orWhereHas('product', function($pq) use ($searchTerm) {
                      $pq->where('product_name', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('user', function($uq) use ($searchTerm) {
                      $uq->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        $wasteList = $query->orderBy('waste_date', 'desc')->orderBy('id', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Waste Inventory');

        // Headers
        $headers = [
            'A1' => '#',
            'B1' => 'Waste Ref #',
            'C1' => 'Date',
            'D1' => 'Item Type',
            'E1' => 'Product Name',
            'F1' => 'Unit',
            'G1' => 'Waste Qty',
            'H1' => 'Reason',
            'I1' => 'Recorded By',
            'J1' => 'Created At',
        ];

        foreach ($headers as $cell => $val) {
            $sheet->setCellValue($cell, $val);
        }

        // Header Styling
        $sheet->getStyle('A1:J1')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle('A1:J1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A');
        $sheet->getStyle('A1:J1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

        $row = 2;
        foreach ($wasteList as $index => $item) {
            $typeLabel = ($item->item_type === 'BAR' || $item->sub_category_id) ? 'Bar Item' : 'Food Item';
            $unitName = $item->unit->name ?? ($item->item_type === 'BAR' ? 'ML' : 'N/A');

            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item->waste_no);
            $sheet->setCellValue('C' . $row, $item->waste_date ? $item->waste_date->format('Y-m-d') : '');
            $sheet->setCellValue('D' . $row, $typeLabel);
            $sheet->setCellValue('E' . $row, $item->product->product_name ?? 'N/A');
            $sheet->setCellValue('F' . $row, $unitName);
            $sheet->setCellValue('G' . $row, (float) $item->waste_qty);
            $sheet->setCellValue('H' . $row, $item->reason ?? '');
            $sheet->setCellValue('I' . $row, $item->user->name ?? 'System');
            $sheet->setCellValue('J' . $row, $item->created_at ? $item->created_at->format('Y-m-d H:i:s') : '');

            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'waste_inventory_' . $fromDate->format('Ymd') . '_' . $toDate->format('Ymd') . '.xlsx';

        return response()->streamDownload(function() use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

