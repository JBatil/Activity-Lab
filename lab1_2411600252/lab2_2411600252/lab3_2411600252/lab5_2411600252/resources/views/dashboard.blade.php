@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex justify-content-between flex-wrap align-items-center pb-2 mb-3 border-bottom">
        <h2>Good day, {{ auth()->user()->name }}!</h2>
        <span class="badge bg-light text-dark border">{{ now()->format('D, M j Y - g:i A') }}</span>
    </div>

    @if ($outOfStockCount > 0 || $lowStockCount > 0)
        <div class="alert alert-warning d-flex align-items-center gap-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-5"></i>
            <div>
                <strong>{{ $lowStockCount }}</strong> product(s) are low on stock and
                <strong>{{ $outOfStockCount }}</strong> product(s) are out of stock.
                <a href="{{ route('products.index', ['status' => 'low_stock']) }}" class="alert-link">Review inventory &rarr;</a>
            </div>
        </div>
    @endif

    <!-- Statistic Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted"><i class="bi bi-box-seam"></i> Total Products</h5>
                    <h2 class="card-text fw-bold text-primary">{{ $totalProducts }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted"><i class="bi bi-cash-stack"></i> Inventory Value</h5>
                    <h2 class="card-text fw-bold text-success">${{ number_format($totalValue, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted"><i class="bi bi-exclamation-triangle"></i> Low Stock</h5>
                    <h2 class="card-text fw-bold text-warning">{{ $lowStockCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card stat-card text-center">
                <div class="card-body">
                    <h5 class="card-title text-muted"><i class="bi bi-x-octagon"></i> Out of Stock</h5>
                    <h2 class="card-text fw-bold text-danger">{{ $outOfStockCount }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-3">
            <div class="card chart-card h-100">
                <div class="card-header"><h6 class="mb-0">Inventory Value by Category</h6></div>
                <div class="card-body"><div class="chart-wrapper"><canvas id="categoryValueChart"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card chart-card h-100">
                <div class="card-header"><h6 class="mb-0">Stock Status Distribution</h6></div>
                <div class="card-body"><div class="chart-wrapper"><canvas id="stockStatusChart"></canvas></div></div>
            </div>
        </div>
        <div class="col-lg-4 mb-3">
            <div class="card chart-card h-100">
                <div class="card-header"><h6 class="mb-0">Top 5 Products by Value</h6></div>
                <div class="card-body"><div class="chart-wrapper"><canvas id="topProductsChart"></canvas></div></div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recently added products -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recently Added Products</h5>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead><tr><th>Name</th><th>Category</th><th>Qty</th><th>Status</th></tr></thead>
                            <tbody>
                                @forelse ($recentProducts as $product)
                                    <tr>
                                        <td><a href="{{ route('products.show', $product) }}">{{ $product->name }}</a></td>
                                        <td>{{ $product->category }}</td>
                                        <td>{{ $product->quantity }}</td>
                                        <td><span class="badge bg-{{ $product->stock_status_color }}">{{ $product->stock_status }}</span></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted text-center py-3">No products yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent inventory transactions -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header"><h5 class="mb-0">Recent Inventory Activity</h5></div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead><tr><th>Time</th><th>Product</th><th>Change</th><th>By</th></tr></thead>
                            <tbody>
                                @forelse ($recentTransactions as $tx)
                                    <tr>
                                        <td class="small text-muted">{{ $tx->created_at->diffForHumans() }}</td>
                                        <td>{{ $tx->product->name ?? 'Deleted product' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $tx->transaction_type === 'stock_in' ? 'success' : 'secondary' }}">
                                                {{ $tx->transaction_type === 'stock_in' ? '+' : '-' }}{{ $tx->quantity }}
                                            </span>
                                        </td>
                                        <td class="small">{{ $tx->user->name ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted text-center py-3">No inventory activity yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const categorySummary = @json($categorySummary);
        const topProducts = @json($topProducts);
        const stockStatus = {
            inStock: {{ $inStockCount }},
            lowStock: {{ $lowStockCount }},
            outOfStock: {{ $outOfStockCount }}
        };
    </script>
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>
@endpush
