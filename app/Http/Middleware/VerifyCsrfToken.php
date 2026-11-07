<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'admin/razorpay/webhook',
        'razorpay/webhook',
        'api/razorpay/webhook',
        'admin/razorpay/*',
        'razorpay/*',
        'api/razorpay/*',
        'restaurant/qr-notifications/*',
    ];

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        // Automatically bypass CSRF for any Razorpay webhook request
        if ($request->hasHeader('X-Razorpay-Signature') || $request->has('event')) {
            return true;
        }

        return parent::inExceptArray($request);
    }
}
