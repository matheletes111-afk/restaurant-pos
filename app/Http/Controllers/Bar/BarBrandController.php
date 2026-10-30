<?php

namespace App\Http\Controllers\Bar;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarBrand;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BarBrandController extends Controller
{
    /**
     * List all Bar Brands for the current restaurant
     */
    public function index(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $brands = BarBrand::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->withCount(['items' => function($q) {
                $q->where('status', '!=', 'D');
            }])
            ->orderBy('id', 'desc')
            ->get();

        $stats = [
            'total'    => $brands->count(),
            'active'   => $brands->where('status', 'A')->count(),
            'inactive' => $brands->where('status', 'I')->count(),
            'with_items' => $brands->where('items_count', '>', 0)->count(),
        ];

        return view('bar.brand_index', compact('brands', 'stats'));
    }

    /**
     * Store a newly created Bar Brand
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status'      => 'nullable|in:A,I',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'name.required' => 'Please enter the brand name.',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        // Check duplicate brand name for this restaurant
        $exists = BarBrand::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->name))])
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'A brand with name "' . $request->name . '" already exists.');
        }

        try {
            $brand = new BarBrand();
            $brand->restaurant_id = $restaurantId;
            $brand->name = trim($request->name);
            $brand->description = $request->description ? trim($request->description) : null;
            $brand->status = $request->status ?? 'A';

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = 'brand-' . time() . '-' . rand(100, 999) . '.' . $image->getClientOriginalExtension();
                $image->move(storage_path('app/public/brands'), $filename);
                $brand->image = $filename;
            }

            $brand->save();

            return redirect()->route('bar.brand.index')
                ->with('success', 'Bar Brand "' . $brand->name . '" created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save brand: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing Bar Brand
     */
    public function update(Request $request)
    {
        $request->validate([
            'id'          => 'required|exists:bar_brands,id',
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status'      => 'nullable|in:A,I',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        $brand = BarBrand::where('id', $request->id)
            ->where('restaurant_id', $restaurantId)
            ->firstOrFail();

        // Check duplicate name excluding current
        $duplicate = BarBrand::where('restaurant_id', $restaurantId)
            ->where('status', '!=', 'D')
            ->where('id', '!=', $brand->id)
            ->whereRaw('LOWER(name) = ?', [strtolower(trim($request->name))])
            ->first();

        if ($duplicate) {
            return redirect()->back()->with('error', 'Another brand with the name "' . $request->name . '" already exists.');
        }

        try {
            $brand->name = trim($request->name);
            $brand->description = $request->description ? trim($request->description) : null;
            $brand->status = $request->status ?? 'A';

            if ($request->hasFile('image')) {
                if ($brand->image && file_exists(storage_path('app/public/brands/' . $brand->image))) {
                    @unlink(storage_path('app/public/brands/' . $brand->image));
                }
                $image = $request->file('image');
                $filename = 'brand-' . time() . '-' . rand(100, 999) . '.' . $image->getClientOriginalExtension();
                $image->move(storage_path('app/public/brands'), $filename);
                $brand->image = $filename;
            }

            $brand->save();

            return redirect()->route('bar.brand.index')
                ->with('success', 'Bar Brand "' . $brand->name . '" updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update brand: ' . $e->getMessage());
        }
    }

    /**
     * Soft delete a Bar Brand
     */
    public function delete($id)
    {
        $restaurantId = auth()->user()->restaurant_id;

        $brand = BarBrand::where('id', $id)
            ->where('restaurant_id', $restaurantId)
            ->first();

        if ($brand) {
            $brand->status = 'D';
            $brand->save();
            return redirect()->route('bar.brand.index')
                ->with('success', 'Bar Brand "' . $brand->name . '" deleted successfully.');
        }

        return redirect()->route('bar.brand.index')
            ->with('error', 'Brand not found or unauthorized.');
    }

    /**
     * Bulk upload Bar Brands from Excel/CSV spreadsheet
     */
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'bulk_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'bulk_file.required' => 'Please select an Excel or CSV file to upload.',
            'bulk_file.mimes'    => 'File must be a valid spreadsheet (.xlsx, .xls, .csv).',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        try {
            $file = $request->file('bulk_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            $existingBrands = BarBrand::where('restaurant_id', $restaurantId)
                ->where('status', '!=', 'D')
                ->pluck('name')
                ->map(fn($n) => strtolower(trim($n)))
                ->toArray();

            $brandsToInsert = [];
            $headerSkipped = false;

            foreach ($rows as $row) {
                if (empty(array_filter($row, fn($val) => !is_null($val) && trim((string)$val) !== ''))) {
                    continue;
                }

                $rawName = trim((string)($row[0] ?? ''));
                $rawDesc = trim((string)($row[1] ?? ''));

                if (!$headerSkipped && in_array(strtolower($rawName), ['brand name', 'brand', 'name', 'bar brand'])) {
                    $headerSkipped = true;
                    continue;
                }

                if ($rawName === '') {
                    continue;
                }

                $key = strtolower($rawName);
                if (in_array($key, $existingBrands) || isset($brandsToInsert[$key])) {
                    continue; // Skip duplicates
                }

                $brandsToInsert[$key] = [
                    'restaurant_id' => $restaurantId,
                    'name'          => $rawName,
                    'description'   => $rawDesc ?: null,
                    'status'        => 'A',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            }

            if (empty($brandsToInsert)) {
                return redirect()->back()->with('error', 'No new valid brands found in the uploaded file.');
            }

            BarBrand::insert(array_values($brandsToInsert));

            return redirect()->route('bar.brand.index')
                ->with('success', 'Successfully imported ' . count($brandsToInsert) . ' bar brands from Excel!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error reading Excel file: ' . $e->getMessage());
        }
    }

    /**
     * Download Excel template for Bar Brands upload
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Brand Name');
        $sheet->setCellValue('B1', 'Description');

        $samples = [
            ['Johnnie Walker', 'Blended Scotch Whisky by Diageo'],
            ['Glenfiddich', 'Single Malt Scotch Whisky produced by William Grant & Sons'],
            ['Grey Goose', 'Premium French Vodka crafted from soft winter wheat'],
            ['Bombay Sapphire', 'Vapour-infused London Dry Gin'],
            ['Corona Extra', 'Mexican Pale Lager brewed by Cervecería Modelo'],
            ['Heineken', 'Dutch Pale Lager Beer with 5% ABV'],
            ['Bira 91', 'Indian Craft Beer brand by B9 Beverages'],
            ['Sula Vineyards', 'Indian red and white wines from Nashik'],
        ];

        $r = 2;
        foreach ($samples as $s) {
            $sheet->setCellValue('A' . $r, $s[0]);
            $sheet->setCellValue('B' . $r, $s[1]);
            $r++;
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF8B5CF6'],
            ],
        ];

        $sheet->getStyle('A1:B1')->applyFromArray($headerStyle);
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(55);

        $writer = new Xlsx($spreadsheet);
        $filename = "bar_brands_upload_template.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
