<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role != 'SA') {
            abort(403, 'Unauthorized access.');
        }

        $quotes = [
            "The only way to do great work is to love what you do. - Steve Jobs",
            "Success is not final, failure is not fatal: it is the courage to continue that counts. - Winston Churchill",
            "Believe you can and you're halfway there. - Theodore Roosevelt",
            "It always seems impossible until it's done. - Nelson Mandela",
            "Don't count the days, make the days count. - Muhammad Ali",
            "Act as if what you do makes a difference. It does. - William James",
            "The future depends on what you do today. - Mahatma Gandhi"
        ];
        
        $quote = $quotes[array_rand($quotes)];

        return view('admin.dashboard', compact('quote'));
    }
}
