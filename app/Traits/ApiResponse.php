<?php

namespace App\Traits;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Facades\Log;

trait ApiResponse
{
    protected function successResponse($data, $message = null, $code = 200)
    {
        $count = 1; // default for single resource/object
        $pagination = null;

        // Normalize: when using ResourceCollection, look at the underlying resource for paginator info
        $underlying = $data instanceof ResourceCollection ? $data->resource : $data;

        if ($underlying instanceof LengthAwarePaginator) {
            // Count items actually returned on this page
            $count = $underlying->count();
            $pagination = [
                'total' => $underlying->total(),
                'per_page' => $underlying->perPage(),
                'current_page' => $underlying->currentPage(),
                'last_page' => $underlying->lastPage(),
                'from' => $underlying->firstItem(),
                'to' => $underlying->lastItem(),
            ];
        } elseif ($underlying instanceof Paginator) {
            // Simple paginator doesn't know the total count
            $count = $underlying->count();
            $pagination = [
                'per_page' => $underlying->perPage(),
                'current_page' => $underlying->currentPage(),
                'has_more_pages' => $underlying->hasMorePages(),
                'next_page_url' => method_exists($underlying, 'nextPageUrl') ? $underlying->nextPageUrl() : null,
                'prev_page_url' => method_exists($underlying, 'previousPageUrl') ? $underlying->previousPageUrl() : null,
            ];
        } elseif ($underlying instanceof CursorPaginator) {
            $count = $underlying->count();
            $pagination = [
                'per_page' => $underlying->perPage(),
                'has_more_pages' => $underlying->hasMorePages(),
                'next_cursor' => optional($underlying->nextCursor())->encode(),
                'prev_cursor' => optional($underlying->previousCursor())->encode(),
            ];
        } elseif ($data instanceof ResourceCollection) {
            // Non-paginated resource collection: count transformed items
            $count = $data->collection instanceof Collection ? $data->collection->count() : $data->count();
        } elseif ($data instanceof JsonResource) {
            $count = 1;
        } elseif ($data instanceof Collection) {
            $count = $data->count();
        } elseif (is_array($data)) {
            // If array has a conventional 'data' key, prefer counting that
            if (array_key_exists('data', $data) && is_countable($data['data'])) {
                $count = count($data['data']);
            } else {
                $count = count($data);
            }
        } elseif (is_null($data)) {
            $count = 0;
        } else {
            // Any other single object/model
            $count = 1;
        }

        $response = [
            'success' => true,
            'message' => $message,
            'timestamp' => now()->toDateTimeString(),
            'total_data' => $count,
            'data' => $data,
        ];

        if ($pagination) {
            $response['pagination'] = $pagination;
        }

        // Add debug info only for non-production
        if (!isProduction()) {
            $response['debug'] = [
                'url' => request()->url(),
                'method' => request()->method(),
            ];
        }

        return response()->json($response, $code);
    }

    protected function errorResponse($message, $code = 400)
    {
        // Store original message for debugging
        $originalMessage = $message;
        
        // Jika message berupa array atau JSON string, konversi ke string
        if (is_array($message) || is_object($message)) {
            $message = collect($message)->flatten()->implode(', ');
        } elseif (is_string($message)) {
            // Coba decode dan proses kalau dia berbentuk JSON string
            $decoded = json_decode($message, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $message = collect($decoded)->flatten()->implode(', ');
            }
        }

        if (isProduction()) {
            Log::error('API Error Response', [
                'message' => $message,
                'code' => $code,
                'url' => request()->url(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            // Generic message for production
            $message = match ($code) {
                400 => 'Bad request. Please check your request and try again.',
                401 => 'Unauthorized access. Please login to continue.',
                403 => 'Forbidden access. You do not have permission to perform this action.',
                404 => 'The requested resource was not found.',
                422 => 'Validation error. Please check your input.',
                429 => 'Too many requests. Please slow down your requests.',
                500, 503 => 'Service temporarily unavailable. Please try again later.',
                default => 'An error occurred while processing your request. Please try again later.',
            };
        }

        $response = [
            'success' => false,
            'message' => $message,
            'timestamp' => now()->toDateTimeString(),
        ];

        // Add debug info only for non-production
        if (!isProduction()) {
            $response['debug'] = [
                'url' => request()->url(),
                'method' => request()->method(),
                'original_message' => $originalMessage,
            ];
        }

        return response()->json($response, $code);
    }
}