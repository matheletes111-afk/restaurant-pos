<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableManage;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\Plan;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableManageController extends Controller
{
    public function index()
    {
        $activeRestId = auth()->user()->restaurant_id;
        $data['tables'] = TableManage::where('status', '!=', 'D')
            ->where('restaurant_id', $activeRestId)
            ->get();
        
        $data['restaurant'] = RestaurantMaster::find($activeRestId);
        $subRestaurantId = auth()->user()->getSubscriptionRestaurantId() ?? $activeRestId;
        $check_plan = Subscription::where('user_id', $subRestaurantId)->active()->first();
        $data['plan_details'] = $check_plan ? Plan::where('id', $check_plan->plan_id)->first() : null;
        
        return view('restaurant.table', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        // SAVE TABLE DETAILS
        $table = new TableManage();
        $table->name = $request->name;
        $table->description = $request->description;
        $table->user_id = auth()->user()->id;
        $table->restaurant_id = auth()->user()->restaurant_id;
        $table->status = 'A';
        $table->save();

        // Generate branded QR Code with logo, QR matrix, and Restaurant Name - Table Name
        $fileName = $this->generateTableQrCode($table);
        $table->qr_code = $fileName;
        $table->save();

        return redirect()->back()->with('success', 'Table added successfully with branded QR Code.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $table = TableManage::where('id', $request->id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();

        $table->name = $request->name;
        $table->description = $request->description;
        $table->save();

        // Re-generate QR Code with updated details
        $fileName = $this->generateTableQrCode($table);
        $table->qr_code = $fileName;
        $table->save();

        return redirect()->back()->with('success', 'Table updated and QR Code regenerated successfully.');
    }

    /**
     * Re-generate a single table QR code (delete old, generate new)
     */
    public function regenerateQr($id)
    {
        $table = TableManage::where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->firstOrFail();

        $fileName = $this->generateTableQrCode($table);
        $table->qr_code = $fileName;
        $table->save();

        return redirect()->back()->with('success', 'QR Code for "' . $table->name . '" has been regenerated successfully.');
    }

    /**
     * Re-generate QR codes for all active tables in this restaurant
     */
    public function regenerateAllQr()
    {
        $tables = TableManage::where('status', '!=', 'D')
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->get();

        $count = 0;
        foreach ($tables as $table) {
            $fileName = $this->generateTableQrCode($table);
            $table->qr_code = $fileName;
            $table->save();
            $count++;
        }

        return redirect()->back()->with('success', "All {$count} table QR codes regenerated successfully.");
    }

    public function status($id)
    {
        $table = TableManage::where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first();

        if ($table) {
            $table->status = $table->status === 'A' ? 'I' : 'A';
            $table->save();
            return redirect()->back()->with('success', 'Status updated successfully.');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }

    public function delete($id)
    {
        $table = TableManage::where('id', $id)
            ->where('restaurant_id', auth()->user()->restaurant_id)
            ->first();

        if ($table) {
            if ($table->qr_code) {
                $oldPath = public_path('qrcodes/' . $table->qr_code);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $table->status = 'D';
            $table->save();
            return redirect()->back()->with('success', 'Table deleted successfully.');
        }
        return redirect()->back()->with('error', 'Record not found.');
    }

    /**
     * Helper to generate a branded SVG QR code card with:
     * - Top: Restaurant Logo (if found) or Styled Restaurant Title
     * - Middle: High resolution scan QR code
     * - Bottom: Restaurant Name - Table Name in bold text + Scan instruction
     */
    protected function generateTableQrCode(TableManage $table): string
    {
        $restaurant = RestaurantMaster::find($table->restaurant_id);
        $restaurantName = $restaurant ? $restaurant->name : 'Restaurant';
        $tableName = $table->name ?? 'Table';
        $fullLabel = $restaurantName . ' - ' . $tableName;

        // Adaptive font sizing based on label length
        $labelLength = mb_strlen($fullLabel);
        if ($labelLength > 40) {
            $fontSize = 13;
        } elseif ($labelLength > 28) {
            $fontSize = 15;
        } else {
            $fontSize = 18;
        }

        $qrLink = url('/order-customer/' . $table->id . '/' . $table->restaurant_id);

        // Generate clean 240x240 SVG QR matrix
        $rawQrSvg = QrCode::format('svg')
            ->size(240)
            ->margin(0)
            ->generate($qrLink);

        // Strip xml header and outer <svg> wrapper to isolate inner elements
        $rawQrSvg = preg_replace('/<\?xml[^>]*\?>/i', '', $rawQrSvg);
        $innerSvg = preg_replace('/^<svg[^>]*>|<\/svg>$/i', '', trim($rawQrSvg));

        // Check for restaurant logo
        $logoSvgElement = '';
        $hasLogo = false;
        if ($restaurant && $restaurant->logo) {
            $logoPath = storage_path('app/public/restaurant/' . $restaurant->logo);
            if (file_exists($logoPath)) {
                $mime = mime_content_type($logoPath) ?: 'image/png';
                $base64Data = base64_encode(file_get_contents($logoPath));
                $dataUri = 'data:' . $mime . ';base64,' . $base64Data;
                $logoSvgElement = '<image href="' . $dataUri . '" x="40" y="16" width="320" height="60" preserveAspectRatio="xMidYMid meet" />';
                $hasLogo = true;
            }
        }

        if (!$hasLogo) {
            $escapedRestName = htmlspecialchars($restaurantName, ENT_XML1, 'UTF-8');
            $topFontSize = mb_strlen($escapedRestName) > 25 ? 16 : 20;
            $logoSvgElement = '<text x="200" y="52" font-family="-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif" font-size="' . $topFontSize . '" font-weight="800" fill="#0f172a" text-anchor="middle">' . $escapedRestName . '</text>';
        }

        $escapedDisplayLabel = htmlspecialchars($fullLabel, ENT_XML1, 'UTF-8');

        // Build standalone composite standee SVG (400x460)
        $svg = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $svg .= '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="400" height="460" viewBox="0 0 400 460">' . "\n";
        
        // Card Background
        $svg .= '  <rect width="400" height="460" rx="18" ry="18" fill="#ffffff" stroke="#e2e8f0" stroke-width="2"/>' . "\n";
        
        // Top Restaurant Logo / Header
        $svg .= '  ' . $logoSvgElement . "\n";
        
        // Accent Divider Bar
        $svg .= '  <line x1="40" y1="84" x2="360" y2="84" stroke="#ff6a00" stroke-width="2" stroke-linecap="round"/>' . "\n";
        
        // QR Container Background
        $svg .= '  <rect x="70" y="98" width="260" height="260" rx="12" ry="12" fill="#ffffff" stroke="#f1f5f9" stroke-width="1.5"/>' . "\n";
        
        // Embedded QR Code (at 80, 108 with size 240)
        $svg .= '  <g transform="translate(80, 108)">' . "\n";
        $svg .= '    ' . $innerSvg . "\n";
        $svg .= '  </g>' . "\n";
        
        // Below QR: Restaurant Name - Table Name
        $svg .= '  <text x="200" y="392" font-family="-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif" font-size="' . $fontSize . '" font-weight="800" fill="#0f172a" text-anchor="middle">' . $escapedDisplayLabel . '</text>' . "\n";
        
        // Helper subtext
        $svg .= '  <text x="200" y="420" font-family="-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif" font-size="13" font-weight="600" fill="#64748b" text-anchor="middle">Scan with camera to View Menu &amp; Order</text>' . "\n";
        
        $svg .= '</svg>';

        // Save to public/qrcodes/
        $qrcodesDir = public_path('qrcodes');
        if (!file_exists($qrcodesDir)) {
            mkdir($qrcodesDir, 0755, true);
        }

        // Delete old QR file
        if ($table->qr_code && file_exists($qrcodesDir . '/' . $table->qr_code)) {
            @unlink($qrcodesDir . '/' . $table->qr_code);
        }

        $fileName = 'qr_' . $table->id . '_' . time() . '.svg';
        $qrPath = $qrcodesDir . '/' . $fileName;

        file_put_contents($qrPath, $svg);

        return $fileName;
    }
}
