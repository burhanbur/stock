<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            App\Http\Middleware\HandleInertiaRequests::class,
        ]);

        $middleware->alias([
            'cors' => App\Http\Middleware\Cors::class,
            'custom.jwt.auth' => App\Http\Middleware\JwtAuthenticate::class,
            'transform.response.keys' => App\Http\Middleware\TransformReponseKeys::class,
            'api.key' => App\Http\Middleware\ValidateApiKey::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (Throwable $e, Request $request) {
            // Handle API requests and JSON requests
            if ($request->is('api/*') || $request->wantsJson()) {

                $statusCode = match (true) {
                    // Validation Exceptions
                    $e instanceof \Illuminate\Validation\ValidationException => 422,
                    
                    // Authentication Exceptions
                    $e instanceof \Illuminate\Auth\AuthenticationException => 401,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException => 401,
                    
                    // Authorization Exceptions
                    $e instanceof \Illuminate\Auth\Access\AuthorizationException => 403,
                    $e instanceof \Illuminate\Auth\Access\AccessDeniedException => 403,
                    
                    // Not Found Exceptions
                    $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException => 404,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException => 404,
                    
                    // HTTP Exceptions
                    $e instanceof \Symfony\Component\HttpKernel\Exception\BadRequestHttpException => 400,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException => 405,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\ConflictHttpException => 409,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException => 429,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException => 503,
                    $e instanceof \Symfony\Component\HttpKernel\Exception\GatewayTimeoutHttpException => 504,
                    
                    // JWT Exceptions
                    $e instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException => 401,
                    $e instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException => 401,
                    $e instanceof Tymon\JWTAuth\Exceptions\TokenBlacklistedException => 401,
                    $e instanceof Tymon\JWTAuth\Exceptions\InvalidClaimException => 401,
                    $e instanceof Tymon\JWTAuth\Exceptions\JWTException => 401,
                    $e instanceof Tymon\JWTAuth\Exceptions\PayloadException => 401,
                    $e instanceof Tymon\JWTAuth\Exceptions\UserNotDefinedException => 401,
                    
                    // Database Exceptions
                    $e instanceof \Illuminate\Database\QueryException => 500,
                    $e instanceof \PDOException => 500,
                    
                    // Default
                    default => 500,
                };

                if (isProduction()) {
                    $message = match ($statusCode) {
                        400 => 'Bad request. Please check your request and try again.',
                        401 => 'Unauthorized access. Please login to continue.',
                        402 => 'Payment required. Please ensure you have the necessary funds to proceed.',
                        403 => 'Forbidden access. You do not have permission to perform this action.',
                        404 => 'The requested resource was not found.',
                        405 => 'Method not allowed. Please check the HTTP method used.',
                        409 => 'Conflict error. The request could not be completed due to a conflict with the current state of the resource.',
                        422 => 'Validation error. Please check your input.',
                        429 => 'Too many requests. You have exceeded the rate limit. Please slow down your requests.',
                        500 => 'Internal server error. Please try again later.',
                        503 => 'Service unavailable. The server is currently unable to handle the request. Please try again later.',
                        504 => 'Gateway timeout. The server took too long to respond. Please try again later.',
                        default => 'An error occurred while processing your request. Please try again later.',
                    };
                    
                    Log::error($message, [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'url' => request()->url(),
                        'method' => request()->method(),
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                        'timestamp' => now()->toDateTimeString(),
                    ]);
                } else {
                    $message = $e->getMessage() ?? 'Internal Server Error';
                }
        
                $response = [
                    'success' => false,
                    'message' => $message,
                    'timestamp' => now()->toDateTimeString(),
                ];

                // Add detailed info only for non-production
                if (!isProduction()) {
                    $response['url'] = request()->url();
                    $response['method'] = request()->method();
                    $response['exception'] = get_class($e);
                    $response['file'] = $e->getFile();
                    $response['line'] = $e->getLine();
                    if ($e->getTrace()) {
                        $response['trace'] = collect($e->getTrace())->take(5)->toArray();
                    }
                }
        
                return response()->json($response, $statusCode);
            }
            
            // Handle web requests - use custom error pages
            if (method_exists($e, 'getStatusCode')) {
                $statusCode = $e->getStatusCode();
                
                // Validate status code (must be valid HTTP status code)
                if ($statusCode < 100 || $statusCode > 599) {
                    $statusCode = 500; // Default to 500 for invalid codes
                }
                
                // Log error untuk web requests juga
                if (config('errors.log_http_errors', true) && $statusCode >= 400) {
                    \Log::channel(config('logging.default'))->log(
                        $statusCode >= 500 ? 'error' : 'warning',
                        "HTTP {$statusCode} Error on web request",
                        [
                            'url' => $request->fullUrl(),
                            'method' => $request->method(),
                            'ip' => $request->ip(),
                            'user_id' => auth()->id(),
                            'user_agent' => $request->userAgent(),
                            'exception' => get_class($e),
                            'message' => $e->getMessage(),
                        ]
                    );
                }
                
                // Render custom error page jika ada
                if (view()->exists("errors.{$statusCode}")) {
                    return response()->view("errors.{$statusCode}", [
                        'exception' => config('app.debug') ? $e : null,
                    ], $statusCode);
                }
                
                // Fallback ke generic error page berdasarkan range
                $fallbackCode = match (true) {
                    $statusCode >= 500 => 500,
                    $statusCode >= 400 => 404,
                    default => 500,
                };
                
                if (view()->exists("errors.{$fallbackCode}")) {
                    return response()->view("errors.{$fallbackCode}", [
                        'exception' => config('app.debug') ? $e : null,
                    ], $statusCode);
                }
            }
            
            return null;
        });
    })->create();
