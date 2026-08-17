<?php

namespace App\Http\Controllers\Stocks;

use App\Actions\Stocks\CompareStocksAction;
use App\Actions\Stocks\GetStockAnalysisAction;
use App\Actions\Stocks\GetStockDetailAction;
use App\Actions\Stocks\ListStockPricesAction;
use App\Actions\Stocks\ListStocksAction;
use App\Actions\Stocks\SyncStockPricesAction;
use App\Actions\Stocks\ToggleWatchlistAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stocks\ListStocksRequest;
use App\Http\Resources\Stocks\SectorResource;
use App\Http\Resources\Stocks\StockDetailResource;
use App\Http\Resources\Stocks\StockListResource;
use App\Http\Resources\Stocks\StockPriceResource;
use App\Models\Sector;
use App\Models\Stock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function index(ListStocksRequest $request, ListStocksAction $action): Response
    {
        $stocks = $action->execute($request->filters(), $request->user()->id)
            ->through(fn ($stock) => (new StockListResource($stock))->resolve());

        return Inertia::render('Stocks/Index', [
            'stocks' => $stocks,
            'sectors' => SectorResource::collection(Sector::orderBy('name')->get())->resolve(),
            'filters' => $request->filters(),
        ]);
    }

    public function show(
        string $ticker,
        Request $request,
        GetStockDetailAction $action,
        ListStockPricesAction $pricesAction,
        GetStockAnalysisAction $analysisAction
    ): Response {
        $stock = $action->execute($ticker, $request->user()->id);
        $stockResource = (new StockDetailResource($stock))->resolve();

        $priceHistory = $pricesAction
            ->execute($stock->id, $request->input('price_sort'), 15)
            ->through(fn ($price) => (new StockPriceResource($price))->resolve());

        $analysis = $stockResource['latest_close'] !== null
            ? $analysisAction->execute($stock->id, $stockResource['latest_close'])
            : ['support_resistance' => ['support' => [], 'resistance' => []], 'backtest' => null];

        return Inertia::render('Stocks/Show', [
            'stock' => $stockResource,
            'priceHistory' => $priceHistory,
            'priceSort' => $request->input('price_sort'),
            'analysis' => $analysis,
        ]);
    }

    public function compare(Request $request, CompareStocksAction $action): Response|RedirectResponse
    {
        $tickers = collect(explode(',', (string) $request->query('tickers', '')))
            ->map(fn ($t) => strtoupper(trim($t)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $result = $action->execute($tickers, $request->user()->id);

        if (count($result['stocks']) < CompareStocksAction::MIN_STOCKS) {
            session()->flash('notification', [
                'level' => 'error',
                'message' => 'Pilih minimal 2 saham valid untuk dibandingkan.',
            ]);

            return redirect()->route('stocks.index');
        }

        if ($result['not_found'] !== []) {
            session()->flash('notification', [
                'level' => 'error',
                'message' => 'Ticker tidak ditemukan: '.implode(', ', $result['not_found']),
            ]);
        }

        return Inertia::render('Stocks/Compare', [
            'stocks' => collect($result['stocks'])
                ->map(fn ($stock) => (new StockDetailResource($stock))->resolve())
                ->values()
                ->all(),
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

    public function toggleWatchlist(string $ticker, Request $request, ToggleWatchlistAction $action): RedirectResponse
    {
        $stock = Stock::query()->where('ticker', strtoupper($ticker))->firstOrFail();

        $action->execute($request->user(), $stock);

        return back();
    }
}
