<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RestaurantMaster;
use App\Models\Subscription;
use App\Models\Plan;
use App\Models\OrderManage;
use App\Models\DemoLead;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (auth()->user()->role != 'SA') {
            abort(403, 'Unauthorized access.');
        }

        $selectedYear = (int) $request->get('year', Carbon::now()->year);
        if ($selectedYear < 2000 || $selectedYear > 2100) {
            $selectedYear = Carbon::now()->year;
        }

        // 1. Monthly Registration vs Monthly Subscriptions data for selected year
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $registrationsQuery = RestaurantMaster::where('status', '!=', 'D')
            ->whereYear('created_at', $selectedYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $subscriptionsQuery = Subscription::whereYear('created_at', $selectedYear)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $monthlyRegistrations = [];
        $monthlySubscriptions = [];

        for ($m = 1; $m <= 12; $m++) {
            $monthlyRegistrations[] = (int) ($registrationsQuery[$m] ?? 0);
            $monthlySubscriptions[] = (int) ($subscriptionsQuery[$m] ?? 0);
        }

        // AJAX response for year switching in the chart
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'year' => $selectedYear,
                'labels' => $months,
                'registrations' => $monthlyRegistrations,
                'subscriptions' => $monthlySubscriptions,
                'totalRegistrationsYear' => array_sum($monthlyRegistrations),
                'totalSubscriptionsYear' => array_sum($monthlySubscriptions),
            ]);
        }

        // Available years for dropdown
        $earliestReg = RestaurantMaster::where('status', '!=', 'D')->min('created_at');
        $earliestSub = Subscription::min('created_at');
        $currentYear = Carbon::now()->year;
        $minYear = $currentYear;
        if ($earliestReg) {
            $minYear = min($minYear, Carbon::parse($earliestReg)->year);
        }
        if ($earliestSub) {
            $minYear = min($minYear, Carbon::parse($earliestSub)->year);
        }
        $availableYears = range(max(2020, $minYear), $currentYear + 1);
        rsort($availableYears);

        // 2. Current Restaurant Counts
        $totalRestaurants = RestaurantMaster::where('status', '!=', 'D')->count();
        $activeRestaurants = RestaurantMaster::where('status', 'A')->count();
        $inactiveRestaurants = RestaurantMaster::where('status', 'I')->count();

        // 3. With Plans Restaurant Count & 4. Restaurant with No Plans
        $restaurantsWithPlanQuery = RestaurantMaster::where('status', '!=', 'D')
            ->whereHas('active_subscription', function ($q) {
                $q->whereIn('status', ['active', 'completed'])
                  ->where(function ($sq) {
                      $sq->whereNull('end_date')->orWhere('end_date', '>=', Carbon::now());
                  });
            });

        $withPlanCount = (clone $restaurantsWithPlanQuery)->count();
        $withoutPlanCount = max(0, $totalRestaurants - $withPlanCount);

        // 5. 30 Days Nearby Expiry Restaurant Count
        $expiringIn30DaysQuery = RestaurantMaster::where('status', '!=', 'D')
            ->whereHas('active_subscription', function ($q) {
                $q->whereIn('status', ['active', 'completed'])
                  ->whereNotNull('end_date')
                  ->whereBetween('end_date', [Carbon::now(), Carbon::now()->copy()->addDays(30)]);
            })
            ->with(['owner', 'active_subscription.plan']);

        $expiringSoonCount = (clone $expiringIn30DaysQuery)->count();
        $expiringSoonRestaurants = $expiringIn30DaysQuery->take(6)->get();

        // 6. Popular Package (Most Subscriptions)
        $popularPackage = Plan::withCount(['subscriptions' => function ($q) {
                $q->whereIn('status', ['active', 'completed']);
            }])
            ->orderByDesc('subscriptions_count')
            ->first();

        if (!$popularPackage || $popularPackage->subscriptions_count == 0) {
            $popularPackage = Plan::withCount('subscriptions')
                ->orderByDesc('subscriptions_count')
                ->first();
        }

        $topPackages = Plan::withCount('subscriptions')
            ->orderByDesc('subscriptions_count')
            ->take(4)
            ->get();

        // 7. Total Order Made By Restaurant Today
        $todayOrdersCount = OrderManage::whereDate('created_at', Carbon::today())->count();
        $todayOrdersAmount = (float) OrderManage::whereDate('created_at', Carbon::today())->sum('grand_total');
        $todayActiveRestaurantsCount = OrderManage::whereDate('created_at', Carbon::today())
            ->distinct('restaurant_id')
            ->count('restaurant_id');

        // 8. Best Restaurant (Maximum number of orders)
        $bestRestaurant = RestaurantMaster::where('status', '!=', 'D')
            ->withCount('orders')
            ->withSum('orders', 'grand_total')
            ->orderByDesc('orders_count')
            ->first();

        // Top 5 restaurants leaderboard
        $topRestaurants = RestaurantMaster::where('status', '!=', 'D')
            ->withCount('orders')
            ->withSum('orders', 'grand_total')
            ->with(['owner', 'active_subscription.plan'])
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        // 9. CRM Leads Pipeline & Status Breakdown
        $crmStats = [
            'total'             => DemoLead::count(),
            'contacted'         => DemoLead::where('status', 'Contacted')->count(),
            'qualified'         => DemoLead::where('status', 'Qualified')->count(),
            'nurturing'         => DemoLead::where('status', 'Nurturing')->count(),
            'converted'         => DemoLead::where('status', 'Converted')->count(),
            'lost'              => DemoLead::where('status', 'Lost')->count(),
            'followups_pending' => DemoLead::whereNotNull('followup_date')->whereDate('followup_date', '>=', Carbon::today())->count(),
        ];

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

        return view('admin.dashboard', compact(
            'quote',
            'selectedYear',
            'availableYears',
            'months',
            'monthlyRegistrations',
            'monthlySubscriptions',
            'totalRestaurants',
            'activeRestaurants',
            'inactiveRestaurants',
            'withPlanCount',
            'withoutPlanCount',
            'expiringSoonCount',
            'expiringSoonRestaurants',
            'popularPackage',
            'topPackages',
            'todayOrdersCount',
            'todayOrdersAmount',
            'todayActiveRestaurantsCount',
            'bestRestaurant',
            'topRestaurants',
            'crmStats'
        ));
    }
}
