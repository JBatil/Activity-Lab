<?php

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\Product;

class DashboardController extends Controller
{
    /**
     * Display the inventory dashboard with real, database-driven statistics.
     */
    public function index()
    {
        $totalProducts = Product::count();
        $totalUnits = (int) Product::sum('quantity');

        // quantity * unit_price summed in PHP to avoid DB-driver decimal quirks
        $totalValue = Product::query()
            ->selectRaw('SUM(quantity * unit_price) as total')
            ->value('total') ?? 0;

        $lowStockCount = Product::lowStock()->count();
        $outOfStockCount = Product::outOfStock()->count();

        $lowStockProducts = Product::lowStock()
            ->orderBy('quantity')
            ->limit(8)
            ->get();

        $outOfStockProducts = Product::outOfStock()
            ->orderBy('name')
            ->limit(8)
            ->get();

        $recentProducts = Product::orderByDesc('created_at')->limit(5)->get();

        $recentTransactions = InventoryTransaction::with(['product', 'user'])
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Inventory value grouped by category, for the bar chart.
        $categorySummary = Product::query()
            ->selectRaw('category, SUM(quantity * unit_price) as total_value, SUM(quantity) as total_quantity, COUNT(*) as product_count')
            ->groupBy('category')
            ->orderByDesc('total_value')
            ->get();

        // Top 5 products by inventory value, for the horizontal bar chart.
        $topProducts = Product::query()
            ->selectRaw('*, (quantity * unit_price) as computed_value')
            ->orderByDesc('computed_value')
            ->limit(5)
            ->get();

        $inStockCount = max($totalProducts - $lowStockCount - $outOfStockCount, 0);

        return view('dashboard', [
            'totalProducts' => $totalProducts,
            'totalUnits' => $totalUnits,
            'totalValue' => (float) $totalValue,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount,
            'inStockCount' => $inStockCount,
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'recentProducts' => $recentProducts,
            'recentTransactions' => $recentTransactions,
            'categorySummary' => $categorySummary,
            'topProducts' => $topProducts,
        ]);
    }
}
