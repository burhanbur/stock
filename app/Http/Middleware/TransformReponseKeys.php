<?php

namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Closure;

class TransformReponseKeys
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        
        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);
            $response->setData($this->transformKeys($data, 'camel'));
        }
        
        return $response;
    }

    private function transformKeys(array $data, string $case): array
    {
        $transformed = [];
        foreach ($data as $key => $value) {
            $newKey = $case === 'camel'
                ? Str::camel($key)
                : Str::snake($key);
            
            $transformed[$newKey] = is_array($value)
                ? $this->transformKeys($value, $case)
                : $value;
        }
        return $transformed;
    }
}
