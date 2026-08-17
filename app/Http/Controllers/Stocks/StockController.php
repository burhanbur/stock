<?php

namespace App\Http\Controllers\Stocks;

use App\Actions\Stocks\GetStockDetailAction;
use App\Actions\Stocks\ListStocksAction;
use App\Actions\Stocks\SyncStockPricesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stocks\ListStocksRequest;
use App\Http\Resources\Stocks\SectorResource;
use App\Http\Resources\Stocks\StockDetailResource;
use App\Http\Resources\Stocks\StockListResource;
use App\Models\Sector;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function index(ListStocksRequest $request, ListStocksAction $action): Response
    {
        $stocks = $action->execute($request->filters())
            ->through(fn ($stock) => (new StockListResource($stock))->resolve());

        return Inertia::render('Stocks/Index', [
            'stocks' => $stocks,
            'sectors' => SectorResource::collection(Sector::orderBy('name')->get())->resolve(),
            'filters' => $request->filters(),
        ]);
    }

    public function show(string $ticker, GetStockDetailAction $action): Response
    {
        $stock = $action->execute($ticker);

        return Inertia::render('Stocks/Show', [
            'stock' => (new StockDetailResource($stock))->resolve(),
        ]);
    }

    public function syncPrices(SyncStockPricesAction $action): RedirectResponse
    {
        $summary = $action->execute();

        $message = $summary['failed'] > 0
            ? "Sinkron selesai: {$summary['synced']} saham diperbarui ({$summary['rows']} baris), {$summary['failed']} gagal."
            : "Sinkron berhasil: {$summary['synced']} saham diperbarui ({$summary['rows']} baris harga).";

        session()->flash('notification', [
            'level' => $summary['synced'] === 0 ? 'error' : 'success',
            'message' => $message,
        ]);

        return back();
    }
}
