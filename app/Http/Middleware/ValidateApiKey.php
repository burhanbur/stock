<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    use ApiResponse;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $apiKey = $request->header('X-API-KEY');

        $error = null;
        $apiKeyModel = null;

        if (empty($apiKey)) {
            $error = $this->errorResponse(
                'API key is required. Please provide X-API-KEY header.',
                401
            );
        } else {
            // Cache API key lookup for 5 minutes to reduce DB queries. Only the
            // raw column values are cached (not the Eloquent model itself) —
            // round-tripping a serialized model through Redis/Predis corrupts
            // it (protected properties come back as __PHP_Incomplete_Class).
            $cacheKey = 'api_key_'.md5($apiKey);
            $cachedAttributes = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($apiKey) {
                return ApiKey::where('key', $apiKey)->first()?->getAttributes();
            });

            $apiKeyModel = $cachedAttributes ? (new ApiKey)->setRawAttributes($cachedAttributes, true) : null;

            if (! $apiKeyModel) {
                Log::warning('Invalid API key attempt', [
                    'api_key' => substr($apiKey, 0, 10).'...',
                    'ip' => $request->ip(),
                    'url' => $request->fullUrl(),
                ]);

                $error = $this->errorResponse('Invalid API key.', 401);
            } elseif (! $apiKeyModel->isValid()) {
                Log::warning('Expired or inactive API key used', [
                    'api_key_id' => $apiKeyModel->id,
                    'application' => $apiKeyModel->application,
                    'ip' => $request->ip(),
                ]);

                $error = $this->errorResponse('API key is expired or inactive.', 401);
            } elseif (! $apiKeyModel->isIpAllowed($request->ip())) {
                Log::warning('API key used from unauthorized IP', [
                    'api_key_id' => $apiKeyModel->id,
                    'application' => $apiKeyModel->application,
                    'ip' => $request->ip(),
                    'allowed_ips' => $apiKeyModel->ip_whitelist,
                ]);

                $error = $this->errorResponse(
                    'Your IP address is not authorized to use this API key.',
                    403
                );
            } elseif ($permission && ! $apiKeyModel->hasPermission($permission)) {
                Log::warning('API key used without proper permission', [
                    'api_key_id' => $apiKeyModel->id,
                    'application' => $apiKeyModel->application,
                    'required_permission' => $permission,
                    'ip' => $request->ip(),
                ]);

                $error = $this->errorResponse(
                    'Insufficient permissions for this endpoint.',
                    403
                );
            } else {
                // Apply custom rate limiting based on API key
                $rateLimitKey = 'api_rate_limit_'.$apiKeyModel->id;
                $attempts = Cache::get($rateLimitKey, 0);

                if ($attempts >= $apiKeyModel->rate_limit) {
                    $error = $this->errorResponse(
                        'Rate limit exceeded. Please try again later.',
                        429
                    );
                } else {
                    Cache::put($rateLimitKey, $attempts + 1, now()->addMinute());

                    // Record usage (async to not block request)
                    dispatch(function () use ($apiKeyModel) {
                        $apiKeyModel->recordUsage();
                    })->afterResponse();

                    // Attach API key to request for later use
                    $request->attributes->set('api_key', $apiKeyModel);
                }
            }
        }

        if (! is_null($error)) {
            return $error;
        }

        return $next($request);
    }
}
