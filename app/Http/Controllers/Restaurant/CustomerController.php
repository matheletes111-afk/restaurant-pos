<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\RestaurantMaster;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomerController extends Controller
{
    /**
     * Resolve active restaurant ID (handles multi-outlets, staff, and standard restaurant logins)
     */
    protected function resolveActiveRestaurantId()
    {
        $user = auth()->user();
        if (!$user) {
            abort(401, 'Unauthenticated');
        }

        if (session()->has('active_outlet_id')) {
            return session('active_outlet_id');
        }

        return method_exists($user, 'getSubscriptionRestaurantId') 
            ? $user->getSubscriptionRestaurantId() 
            : $user->restaurant_id;
    }

    /**
     * Retrieve aggregated, deduplicated customer directory from orders table.
     * - Filters out walk-in customers
     * - Requires customer name to be provided
     * - Groups by common mobile number so identical phone numbers never repeat
     * - If phone or email is missing, sets as null (displayed as "Not Found")
     */
    protected function getAggregatedCustomers($restaurantId, $keyword = null)
    {
        // 1. Fetch orders from orders table with valid customer names, excluding walk-ins
        $query = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->whereNotNull('customer_name')
            ->whereRaw("TRIM(customer_name) != ''")
            ->whereRaw("LOWER(TRIM(customer_name)) NOT LIKE '%walk%in%'")
            ->whereRaw("LOWER(TRIM(customer_name)) NOT IN ('walk in', 'walk-in', 'walkin', 'walk-in customer', 'walk in customer', 'walk in guest', 'walk-in guest', 'guest')")
            ->whereNull('deleted_at');

        $orders = $query->select(
                'id',
                'customer_name',
                'customer_phone',
                'customer_email',
                'grand_total',
                'amount_paid',
                'created_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Also check if any manual records exist in customers table for supplementary email/notes
        $savedCustomerMap = [];
        if (\Illuminate\Support\Facades\Schema::hasTable('customers')) {
            $savedCustomers = DB::table('customers')
                ->where('restaurant_id', $restaurantId)
                ->get();
            foreach ($savedCustomers as $sc) {
                if (!empty($sc->phone)) {
                    $savedCustomerMap[trim($sc->phone)] = $sc;
                }
            }
        }

        // 3. Aggregate orders by common phone number, or by name if no phone provided
        $customersByPhone = [];
        $customersWithoutPhone = [];

        foreach ($orders as $order) {
            $rawPhone = trim($order->customer_phone ?? '');
            $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
            $hasPhone = strlen($cleanPhone) >= 6;

            $name = trim($order->customer_name ?? '');
            $email = trim($order->customer_email ?? '');
            $orderTotal = floatval($order->amount_paid ?: $order->grand_total);
            $orderDate = $order->created_at ? Carbon::parse($order->created_at) : null;

            if ($hasPhone) {
                // Group by common phone number so mobile number never repeats
                if (!isset($customersByPhone[$cleanPhone])) {
                    // Check if supplementary email was saved in customers table
                    $savedRecord = $savedCustomerMap[$rawPhone] ?? ($savedCustomerMap[$cleanPhone] ?? null);
                    $initialEmail = $email ?: ($savedRecord ? $savedRecord->email : null);

                    $customersByPhone[$cleanPhone] = (object) [
                        'name' => $name,
                        'phone' => $rawPhone ?: $cleanPhone,
                        'clean_phone' => $cleanPhone,
                        'email' => $initialEmail ?: null,
                        'total_orders' => 0,
                        'total_spent' => 0.0,
                        'last_order_date' => $orderDate,
                    ];
                }

                $customersByPhone[$cleanPhone]->total_orders++;
                $customersByPhone[$cleanPhone]->total_spent += $orderTotal;

                if (empty($customersByPhone[$cleanPhone]->email) && !empty($email)) {
                    $customersByPhone[$cleanPhone]->email = $email;
                }

                // If a later order has a more complete name, pick the longer/better name
                if (strlen($name) > strlen($customersByPhone[$cleanPhone]->name)) {
                    $customersByPhone[$cleanPhone]->name = $name;
                }

                // Update latest order date if this order is newer
                if ($orderDate && (!$customersByPhone[$cleanPhone]->last_order_date || $orderDate->gt($customersByPhone[$cleanPhone]->last_order_date))) {
                    $customersByPhone[$cleanPhone]->last_order_date = $orderDate;
                }
            } else {
                // No phone provided: group by normalized customer name
                $normName = strtolower($name);
                if (!isset($customersWithoutPhone[$normName])) {
                    $customersWithoutPhone[$normName] = (object) [
                        'name' => $name,
                        'phone' => null,
                        'clean_phone' => null,
                        'email' => $email ?: null,
                        'total_orders' => 0,
                        'total_spent' => 0.0,
                        'last_order_date' => $orderDate,
                    ];
                }

                $customersWithoutPhone[$normName]->total_orders++;
                $customersWithoutPhone[$normName]->total_spent += $orderTotal;

                if (empty($customersWithoutPhone[$normName]->email) && !empty($email)) {
                    $customersWithoutPhone[$normName]->email = $email;
                }

                if ($orderDate && (!$customersWithoutPhone[$normName]->last_order_date || $orderDate->gt($customersWithoutPhone[$normName]->last_order_date))) {
                    $customersWithoutPhone[$normName]->last_order_date = $orderDate;
                }
            }
        }

        // Merge all customers (those with phone numbers first, then those without)
        $all = collect(array_merge(array_values($customersByPhone), array_values($customersWithoutPhone)));

        // 4. Keyword search filter if provided
        if (!empty($keyword)) {
            $kw = strtolower(trim($keyword));
            $all = $all->filter(function ($c) use ($kw) {
                return (strpos(strtolower($c->name ?? ''), $kw) !== false)
                    || (strpos(strtolower($c->phone ?? ''), $kw) !== false)
                    || (strpos(strtolower($c->email ?? ''), $kw) !== false);
            });
        }

        // 5. Sort by last_order_date descending by default
        return $all->sortByDesc(function ($c) {
            return $c->last_order_date ? $c->last_order_date->timestamp : 0;
        })->values();
    }

    /**
     * Display Customer List in Restaurant Panel
     */
    public function index(Request $request)
    {
        $restaurantId = $this->resolveActiveRestaurantId();
        $restaurant = RestaurantMaster::find($restaurantId);

        $keyword = $request->input('keyword', $request->input('search'));
        $customers = $this->getAggregatedCustomers($restaurantId, $keyword);

        // High-level KPI metrics
        $totalCustomersCount = $customers->count();
        $customersWithPhone = $customers->whereNotNull('phone')->count();
        $customersWithEmail = $customers->whereNotNull('email')->count();
        $totalCustomerSpent = $customers->sum('total_spent');
        $repeatCustomersCount = $customers->where('total_orders', '>', 1)->count();

        return view('restaurant.customers.index', compact(
            'customers',
            'restaurant',
            'keyword',
            'totalCustomersCount',
            'customersWithPhone',
            'customersWithEmail',
            'totalCustomerSpent',
            'repeatCustomersCount'
        ));
    }

    /**
     * Export Customer List to Excel (.xlsx)
     */
    public function export(Request $request)
    {
        $restaurantId = $this->resolveActiveRestaurantId();
        $restaurant = RestaurantMaster::find($restaurantId);

        $keyword = $request->input('keyword', $request->input('search'));
        $customers = $this->getAggregatedCustomers($restaurantId, $keyword);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer List');

        // Document Metadata
        $spreadsheet->getProperties()
            ->setCreator(config('app.name', 'Bill&Bite'))
            ->setTitle('Customer List - ' . ($restaurant ? $restaurant->name : 'Restaurant'));

        // Restaurant Brand Header
        $restaurantName = $restaurant ? $restaurant->name : 'Restaurant';
        $sheet->setCellValue('A1', strtoupper($restaurantName) . ' - CUSTOMER DIRECTORY');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF1E293B');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Subtitle / Generated Date
        $subtitle = 'Generated on ' . Carbon::now()->format('d M Y, h:i A') . ' | Total Unique Customers: ' . $customers->count();
        if (!empty($keyword)) {
            $subtitle .= ' | Search Keyword: "' . $keyword . '"';
        }
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Table Header
        $headers = [
            'A3' => '#',
            'B3' => 'Customer Name',
            'C3' => 'Mobile Number',
            'D3' => 'Email Address',
            'E3' => 'Total Orders',
            'F3' => 'Total Spent (₹)',
            'G3' => 'Last Order Date'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFF6A00']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]
            ]
        ];
        $sheet->getStyle('A3:G3')->applyFromArray($headerStyle);
        $sheet->getRowDimension(3)->setRowHeight(26);

        // Populate Data Rows
        $row = 4;
        $sno = 1;

        foreach ($customers as $c) {
            $phoneDisplay = !empty($c->phone) ? $c->phone : 'Not Found';
            $emailDisplay = !empty($c->email) ? $c->email : 'Not Found';
            $lastOrderDisplay = $c->last_order_date ? $c->last_order_date->format('d M Y, h:i A') : 'Not Found';

            $sheet->setCellValue('A' . $row, $sno++);
            $sheet->setCellValue('B' . $row, $c->name ?: 'Not Found');
            $sheet->setCellValueExplicit('C' . $row, $phoneDisplay, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $emailDisplay);
            $sheet->setCellValue('E' . $row, $c->total_orders);
            $sheet->setCellValue('F' . $row, round($c->total_spent, 2));
            $sheet->setCellValue('G' . $row, $lastOrderDisplay);

            // Alignment & Styling
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Alternate row fill
            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFC');
            }

            // Light borders
            $sheet->getStyle('A' . $row . ':G' . $row)->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');

            $row++;
        }

        // Summary Total Row
        if ($customers->count() > 0) {
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->mergeCells('A' . $row . ':D' . $row);
            $sheet->setCellValue('E' . $row, $customers->sum('total_orders'));
            $sheet->setCellValue('F' . $row, round($customers->sum('total_spent'), 2));
            $sheet->setCellValue('G' . $row, $customers->count() . ' Unique Customers');

            $totalStyle = [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFE4D6']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFFF6A00']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FFFF6A00']]
                ]
            ];
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($totalStyle);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($row)->setRowHeight(24);
        }

        // Auto-fit Column Dimensions
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $cleanRestaurantSlug = preg_replace('/[^A-Za-z0-9_-]/', '_', $restaurantName);
        $filename = "Customer_List_" . $cleanRestaurantSlug . "_" . Carbon::now()->format('Ymd_His') . ".xlsx";

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}

