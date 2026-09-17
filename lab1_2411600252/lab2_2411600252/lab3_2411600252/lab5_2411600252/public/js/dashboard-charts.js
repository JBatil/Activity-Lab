// Renders the three dashboard charts using real database values that were
// injected into the page as `categorySummary`, `topProducts`, and `stockStatus`
// (see resources/views/dashboard.blade.php). This replaces the Lab 3/4 version
// that pulled data from a static data/products.json file via fetch().

const CHART_PALETTE = ['#4F46E5', '#6366F1', '#10B981', '#f3a712', '#17a2b8', '#dc3545', '#0EA5E9', '#8B5CF6'];
const STATUS_COLORS = { 'In Stock': '#10B981', 'Low Stock': '#f3a712', 'Out of Stock': '#dc3545' };

const baseOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { labels: { font: { size: 11 } } } }
};

function renderCategoryValueChart(categorySummary) {
    const ctx = document.getElementById('categoryValueChart');
    if (!ctx) return;

    const labels = categorySummary.map(c => c.category);
    const values = categorySummary.map(c => Number(parseFloat(c.total_value).toFixed(2)));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{ label: 'Inventory Value ($)', data: values, backgroundColor: CHART_PALETTE, borderRadius: 6 }]
        },
        options: {
            ...baseOptions,
            plugins: { ...baseOptions.plugins, legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
}

function renderStockStatusChart(stockStatus) {
    const ctx = document.getElementById('stockStatusChart');
    if (!ctx) return;

    const labels = ['In Stock', 'Low Stock', 'Out of Stock'];
    const data = [stockStatus.inStock, stockStatus.lowStock, stockStatus.outOfStock];
    const colors = labels.map(l => STATUS_COLORS[l]);

    new Chart(ctx, {
        type: 'doughnut',
        data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#fff' }] },
        options: { ...baseOptions, cutout: '65%' }
    });
}

function renderTopProductsChart(topProducts) {
    const ctx = document.getElementById('topProductsChart');
    if (!ctx) return;

    const labels = topProducts.map(p => p.name);
    const values = topProducts.map(p => Number((p.quantity * parseFloat(p.unit_price)).toFixed(2)));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{ label: 'Inventory Value ($)', data: values, backgroundColor: CHART_PALETTE[1], borderRadius: 6 }]
        },
        options: {
            ...baseOptions,
            indexAxis: 'y',
            plugins: { ...baseOptions.plugins, legend: { display: false } },
            scales: { x: { beginAtZero: true } }
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof categorySummary !== 'undefined') renderCategoryValueChart(categorySummary);
    if (typeof stockStatus !== 'undefined') renderStockStatusChart(stockStatus);
    if (typeof topProducts !== 'undefined') renderTopProductsChart(topProducts);
});
