<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureWebhookRequestIsSecure
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isSecure() && ! app()->isProduction()) {
            return $next($request);
        }

        if (app()->isProduction() && ! $request->isSecure()) {
            abort(403, 'Webhook requests must use HTTPS.');
        }

        $forwardedProto = $request->header('X-Forwarded-Proto');

        if (app()->isProduction() && filled($forwardedProto) && strtolower((string) $forwardedProto) !== 'https') {
            abort(403, 'Invalid forwarded protocol.');
        }

        if (! $request->hasHeader('User-Agent')) {
            abort(403, 'Missing User-Agent header.');
        }

        return $next($request);
    }
}
