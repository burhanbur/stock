<?php

namespace App\Http\Controllers\Api\Stocks;

use App\Actions\Stocks\GetStockDetailAction;
use App\Actions\Stocks\ListStocksAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stocks\ListStocksRequest;
use App\Http\Resources\Stocks\StockDetailResource;
use App\Http\Resources\Stocks\StockListResource;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class StockController extends Controller
{
    use ApiResponse;

    public function index(ListStocksRequest $request, ListStocksAction $action): JsonResponse
    {
        $stocks = $action->execute($request->filters());

        return $this->successResponse(StockListResource::collection($stocks));
    }

    public function show(string $ticker, GetStockDetailAction $action): JsonResponse
    {
        try {
            $stock = $action->execute($ticker);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }

        return $this->successResponse(new StockDetailResource($stock));
    }
}
