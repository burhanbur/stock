<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Traits\ApiResponse;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;

class JwtAuthenticate
{
    use ApiResponse;

    public function handle(Request $request, Closure $next)
    {
        try {
            // Get bearer token from Authorization header
            $token = $request->bearerToken();

            if (! $token) {
                return $this->errorResponse('Authorization token not provided', 401);
            }

            $user = JWTAuth::setToken($token)->authenticate();

            if (! $user) {
                return $this->errorResponse('User not found for provided token', 401);
            }

            // Bind authenticated user into the current auth context
            auth()->setUser($user);

            return $next($request);
        } catch (TokenExpiredException | TokenInvalidException | UserNotDefinedException | JWTException $e) {
            // Re-throw so bootstrap/app.php exception handler can convert to JSON response
            throw $e;
        } catch (\Exception $e) {
            if (function_exists('isProduction') && isProduction()) {
                return $this->errorResponse('Unauthorized access. Please login to continue.', 401);
            }

            throw $e;
        }
    }
}