export interface Sector {
    id: string;
    code: string;
    name: string;
}

export interface StockListItem {
    id: string;
    ticker: string;
    exchange: string;
    currency: string;
    company_name: string | null;
    sector: Sector | null;
    latest_close: number | null;
    latest_trading_date: string | null;
}

export interface StockPricePoint {
    trading_date: string;
    open: number;
    high: number;
    low: number;
    close: number;
    volume: number;
}

export interface StockDetail {
    id: string;
    ticker: string;
    exchange: string;
    board: string | null;
    currency: string;
    listed_at: string | null;
    is_active: boolean;
    company: {
        id: string;
        name: string;
        short_name: string | null;
        description: string | null;
    };
    sector: Sector | null;
    latest_close: number | null;
    latest_trading_date: string | null;
    change: number;
    change_percent: number;
    prices: StockPricePoint[];
}

export interface StockListFilters {
    search: string | null;
    sector_id: string | null;
    sort: string | null;
    per_page: number | null;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

/** Mirrors Laravel's default LengthAwarePaginator::toArray() shape. */
export interface PaginatedResponse<T> {
    data: T[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    links: PaginationLink[];
    first_page_url: string;
    last_page_url: string;
    next_page_url: string | null;
    prev_page_url: string | null;
    path: string;
}
