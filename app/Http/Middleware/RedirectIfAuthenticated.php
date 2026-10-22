<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                if ($user) {
                    if (method_exists($user, 'getDashboardRedirectUrl')) {
                        return redirect($user->getDashboardRedirectUrl());
                    }

                    if (isset($user->role) && $user->role === 'SA') {
                        return redirect()->route('admin.dashboard');
                    }

                    $subRestId = method_exists($user, 'getSubscriptionRestaurantId') ? $user->getSubscriptionRestaurantId() : $user->restaurant_id;
                    if (!empty($subRestId)) {
                        $active = DB::table('subscriptions')
                            ->where('user_id', $subRestId)
                            ->where(function ($query) {
                                $query->where('status', 'active')
                                      ->orWhere(function ($q) {
                                          $q->where('status', 'completed')
                                            ->whereDate('end_date', '>=', now());
                                      });
                            })
                            ->latest('id')
                            ->first();

                        if (!$active) {
                            return redirect()->route('select.plan.page');
                        }
                    }

                    return redirect()->route('dashboard');
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}
