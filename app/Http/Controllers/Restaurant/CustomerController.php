<?php

namespace App\Http\Controllers\Restaurant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
use App\Models\RestaurantMaster;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CustomerController extends Controller
{
    /**
     * Display a listing of unique customers from the orders table.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $restaurantId = $user->restaurant_id;

        if ($request->has('outlet_id')) {
            $requestedId = (int) $request->get('outlet_id');
            $mainRest = method_exists($user, 'getMainRestaurant') ? $user->getMainRestaurant() : null;
            if ($mainRest && ($mainRest->id === $requestedId || RestaurantMaster::where('parent_id', $mainRest->id)->where('id', $requestedId)->exists())) {
                $restaurantId = $requestedId;
            }
        }

        $restaurant = RestaurantMaster::find($restaurantId);

        $keyword = trim($request->input('keyword', $request->input('search', '')));
        $sortBy = $request->input('sort', 'latest');

        // Fetch aggregated unique customers
        $allCustomers = $this->getUniqueCustomersFromOrders($restaurantId);

        // Filter by keyword if provided
        if (!empty($keyword)) {
            $kwLower = strtolower($keyword);
            $allCustomers = $allCustomers->filter(function ($c) use ($kwLower) {
                return str_contains(strtolower($c->name), $kwLower)
                    || str_contains($c->phone, $kwLower)
                    || str_contains($c->clean_phone, $kwLower);
            });
        }

        // Sorting
        if ($sortBy === 'orders') {
            $allCustomers = $allCustomers->sortByDesc('total_orders');
        } elseif ($sortBy === 'spent') {
            $allCustomers = $allCustomers->sortByDesc('total_spent');
        } elseif ($sortBy === 'name') {
            $allCustomers = $allCustomers->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
        } else {
            $allCustomers = $allCustomers->sortByDesc('last_order_date');
        }

        // Summary Statistics
        $totalCustomersCount = $allCustomers->count();
        $totalOrdersCount = $allCustomers->sum('total_orders');
        $totalRevenue = $allCustomers->sum('total_spent');
        $repeatCustomersCount = $allCustomers->where('total_orders', '>', 1)->count();

        // Paginate results
        $perPage = 25;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allCustomers->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $customers = new LengthAwarePaginator(
            $currentItems,
            $allCustomers->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('restaurant.customers.index', compact(
            'restaurant',
            'customers',
            'keyword',
            'sortBy',
            'totalCustomersCount',
            'totalOrdersCount',
            'totalRevenue',
            'repeatCustomersCount'
        ));
    }

    /**
     * Export customer list to Excel (.xlsx)
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $restaurantId = $user->restaurant_id;

        if ($request->has('outlet_id')) {
            $requestedId = (int) $request->get('outlet_id');
            $mainRest = method_exists($user, 'getMainRestaurant') ? $user->getMainRestaurant() : null;
            if ($mainRest && ($mainRest->id === $requestedId || RestaurantMaster::where('parent_id', $mainRest->id)->where('id', $requestedId)->exists())) {
                $restaurantId = $requestedId;
            }
        }

        $restaurant = RestaurantMaster::find($restaurantId);
        $restaurantName = $restaurant ? $restaurant->name : 'Restaurant';

        $keyword = trim($request->input('keyword', $request->input('search', '')));
        $customers = $this->getUniqueCustomersFromOrders($restaurantId);

        if (!empty($keyword)) {
            $kwLower = strtolower($keyword);
            $customers = $customers->filter(function ($c) use ($kwLower) {
                return str_contains(strtolower($c->name), $kwLower)
                    || str_contains($c->phone, $kwLower)
                    || str_contains($c->clean_phone, $kwLower);
            });
        }

        $customers = $customers->sortByDesc('last_order_date');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Customer List');

        // Header Title
        $sheet->setCellValue('A1', strtoupper($restaurantName) . ' - CUSTOMER DIRECTORY');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFFFF'));
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0F172A');
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(32);

        $subtitle = 'Generated on ' . Carbon::now()->format('d M Y, h:i A') . ' | Total Unique Customers: ' . $customers->count();
        if (!empty($keyword)) {
            $subtitle .= ' | Search: "' . $keyword . '"';
        }
        $sheet->setCellValue('A2', $subtitle);
        $sheet->mergeCells('A2:G2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF64748B'));
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(2)->setRowHeight(20);

        // Column Headers
        $headers = [
            'A3' => '#',
            'B3' => 'Customer Name',
            'C3' => 'Mobile Number',
            'D3' => 'Total Orders',
            'E3' => 'Total Spent (₹)',
            'F3' => 'Last Order Date',
            'G3' => 'WhatsApp Contact'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF2563EB']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFE2E8F0']]]
        ];
        $sheet->getStyle('A3:G3')->applyFromArray($headerStyle);
        $sheet->getRowDimension(3)->setRowHeight(26);

        $row = 4;
        $sno = 1;

        foreach ($customers as $c) {
            $sheet->setCellValue('A' . $row, $sno++);
            $sheet->setCellValue('B' . $row, $c->name);
            $sheet->setCellValueExplicit('C' . $row, $c->phone, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row, $c->total_orders);
            $sheet->setCellValue('E' . $row, round($c->total_spent, 2));
            $sheet->setCellValue('F' . $row, $c->last_order_date ? $c->last_order_date->format('d M Y, h:i A') : '-');
            $sheet->setCellValue('G' . $row, 'https://wa.me/' . $c->wa_phone);

            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            if ($row % 2 == 0) {
                $sheet->getStyle('A' . $row . ':G' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF8FAFC');
            }
            $sheet->getStyle('A' . $row . ':G' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setARGB('FFE2E8F0');
            $row++;
        }

        // Totals row
        if ($customers->count() > 0) {
            $sheet->setCellValue('A' . $row, 'TOTAL');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $sheet->setCellValue('D' . $row, $customers->sum('total_orders'));
            $sheet->setCellValue('E' . $row, round($customers->sum('total_spent'), 2));
            $sheet->setCellValue('F' . $row, $customers->count() . ' Customers');
            $sheet->setCellValue('G' . $row, '');

            $totalStyle = [
                'font' => ['bold' => true, 'color' => ['argb' => 'FF0F172A'], 'size' => 11],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFE0E7FF']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FF2563EB']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['argb' => 'FF2563EB']]
                ]
            ];
            $sheet->getStyle('A' . $row . ':G' . $row)->applyFromArray($totalStyle);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getRowDimension($row)->setRowHeight(24);
        }

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $cleanSlug = preg_replace('/[^A-Za-z0-9_-]/', '_', $restaurantName);
        $filename = "Customers_" . $cleanSlug . "_" . Carbon::now()->format('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Helper to query and aggregate unique customers from orders table
     */
    protected function getUniqueCustomersFromOrders($restaurantId)
    {
        $orders = DB::table('orders')
            ->where('restaurant_id', $restaurantId)
            ->whereNotNull('customer_phone')
            ->whereRaw("TRIM(customer_phone) != ''")
            ->whereRaw("TRIM(customer_phone) != 'N/A'")
            ->whereNotNull('customer_name')
            ->whereRaw("TRIM(customer_name) != ''")
            ->whereNull('deleted_at')
            ->select('id', 'order_id', 'customer_name', 'customer_phone', 'grand_total', 'amount_paid', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $customersMap = [];

        foreach ($orders as $order) {
            $rawPhone = trim($order->customer_phone ?? '');
            $digits = preg_replace('/[^0-9]/', '', $rawPhone);

            // Minimum length check for a valid mobile number
            if (strlen($digits) < 6) {
                continue;
            }

            // Normalize mobile number for deduplication (handle leading 0 or 91 country code for Indian numbers)
            if (strlen($digits) === 11 && str_starts_with($digits, '0')) {
                $cleanPhone = substr($digits, 1);
            } elseif (strlen($digits) === 12 && str_starts_with($digits, '91')) {
                $cleanPhone = substr($digits, 2);
            } else {
                $cleanPhone = $digits;
            }

            $name = trim($order->customer_name ?? '');

            // Skip empty or generic walk-in names
            if (empty($name)) {
                continue;
            }
            $lowerName = strtolower($name);
            if (in_array($lowerName, ['walk in', 'walk-in', 'walkin', 'walk in guest', 'walk-in guest', 'walk-in customer', 'walk in customer', 'guest', 'table order', 'customer'])) {
                continue;
            }

            // WhatsApp number with country code
            $waPhone = (strlen($cleanPhone) === 10) ? ('91' . $cleanPhone) : $digits;

            $orderAmount = floatval($order->grand_total ?: $order->amount_paid ?: 0);
            $orderDate = $order->created_at ? Carbon::parse($order->created_at) : null;

            if (!isset($customersMap[$cleanPhone])) {
                $customersMap[$cleanPhone] = (object) [
                    'name' => $name,
                    'phone' => $rawPhone ?: $cleanPhone,
                    'clean_phone' => $cleanPhone,
                    'wa_phone' => $waPhone,
                    'total_orders' => 0,
                    'total_spent' => 0.0,
                    'first_order_date' => $orderDate,
                    'last_order_date' => $orderDate,
                ];
            }

            $c = $customersMap[$cleanPhone];
            $c->total_orders++;
            $c->total_spent += $orderAmount;

            if ($orderDate && (!$c->last_order_date || $orderDate->gt($c->last_order_date))) {
                $c->last_order_date = $orderDate;
            }
            if ($orderDate && (!$c->first_order_date || $orderDate->lt($c->first_order_date))) {
                $c->first_order_date = $orderDate;
            }

            // Keep the most complete / longest name
            if (strlen($name) > strlen($c->name) && !str_contains($lowerName, 'walk')) {
                $c->name = $name;
            }
        }

        return collect(array_values($customersMap));
    }
}
