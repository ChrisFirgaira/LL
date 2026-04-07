<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyStorefrontApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $configuredKey = (string) config('services.storefront_api.key');

        if ($configuredKey === '') {
            return new JsonResponse([
                'message' => 'Storefront API key is not configured.',
            ], 503);
        }

        $providedKey = (string) ($request->header('X-Storefront-Api-Key') ?? '');

        if (! hash_equals($configuredKey, $providedKey)) {
            return new JsonResponse([
                'message' => 'Unauthorized.',
            ], 401);
        }

        return $next($request);
    }
}
