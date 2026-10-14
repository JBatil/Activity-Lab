const ChartManager = (function () {
    const charts = {};
    const valueHistory = { labels: [], values: [] };

    const GRID = 'rgba(0, 0, 0, 0.06)';
    const TICK = { color: '#555', font: { size: 10 } };

    function titleOpt(text) {
        return {
            display: true,
            text,
            color: '#555',
            font: { size: 11, weight: 'bold' },
            padding: { top: 0, bottom: 14 }
        };
    }

    function baseOptions(title) {
        return {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: titleOpt(title),
                legend: { labels: { font: { size: 10 }, color: '#555', boxWidth: 38, boxHeight: 12 } }
            }
        };
    }

    // Create the chart the first time, update it in place afterwards (no flicker on live refresh)
    function upsert(key, canvasId, config) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;
        if (charts[key]) {
            charts[key].data = config.data;
            charts[key].options = config.options;
            charts[key].update();
        } else {
            charts[key] = new Chart(ctx, config);
        }
    }

    // 1. Products by Category (doughnut)
    function renderCategoryCountChart(categorySummary, total) {
        const opts = baseOptions(`Products by Category (${total} total)`);
        opts.cutout = '45%';
        opts.plugins.legend = { ...opts.plugins.legend, position: 'right' };
        upsert('categoryCount', 'categoryCountChart', {
            type: 'doughnut',
            data: {
                labels: categorySummary.map(c => c.category),
                datasets: [{
                    data: categorySummary.map(c => c.productCount),
                    backgroundColor: CONFIG.categoryPalette,
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: opts
        });
    }

    // 2. Stock Health by Category (100% stacked bar)
    function renderStockHealthChart(health) {
        const opts = baseOptions('Stock Health by Category (% of items)');
        opts.plugins.legend = { ...opts.plugins.legend, position: 'top' };
        opts.plugins.tooltip = {
            callbacks: { label: c => `${c.dataset.label}: ${c.parsed.y.toFixed(1)}%` }
        };
        opts.scales = {
            x: { stacked: true, grid: { color: GRID }, ticks: TICK },
            y: { stacked: true, min: 0, max: 100, grid: { color: GRID }, ticks: TICK }
        };
        const c = CONFIG.stockHealthColors;
        upsert('stockHealth', 'stockHealthChart', {
            type: 'bar',
            data: {
                labels: health.map(h => h.category),
                datasets: [
                    { label: 'In Stock (%)', data: health.map(h => h.inStockPct), backgroundColor: c['In Stock'], borderColor: '#fff', borderWidth: 1 },
                    { label: 'Low Stock (%)', data: health.map(h => h.lowStockPct), backgroundColor: c['Low Stock'], borderColor: '#fff', borderWidth: 1 },
                    { label: 'Out of Stock (%)', data: health.map(h => h.outOfStockPct), backgroundColor: c['Out of Stock'], borderColor: '#fff', borderWidth: 1 }
                ]
            },
            options: opts
        });
    }

    // 3. Stock vs Reorder Level (horizontal grouped bar)
    function renderReorderChart(items) {
        const opts = baseOptions('Stock vs Reorder Level (items needing attention)');
        opts.indexAxis = 'y';
        opts.plugins.legend = { ...opts.plugins.legend, position: 'top' };
        opts.scales = {
            x: { beginAtZero: true, grid: { color: GRID }, ticks: TICK },
            y: { grid: { display: false }, ticks: TICK }
        };
        upsert('reorder', 'reorderChart', {
            type: 'bar',
            data: {
                labels: items.map(p => p.name),
                datasets: [
                    { label: 'Quantity', data: items.map(p => p.quantity), backgroundColor: CONFIG.attentionColors.quantity, barPercentage: 0.9, categoryPercentage: 0.85 },
                    { label: 'Reorder Level', data: items.map(p => p.reorder_level), backgroundColor: CONFIG.attentionColors.reorder, barPercentage: 0.9, categoryPercentage: 0.85 }
                ]
            },
            options: opts
        });
    }

    // 4. Total Inventory Value (live line) – appends a point on every refresh
    function renderLiveValueChart(totalValue) {
        valueHistory.labels.push(new Date().toLocaleTimeString());
        valueHistory.values.push(Number(totalValue.toFixed(2)));
        while (valueHistory.labels.length > CONFIG.liveHistoryMaxPoints) {
            valueHistory.labels.shift();
            valueHistory.values.shift();
        }

        const opts = baseOptions('Total Inventory Value (live)');
        opts.plugins.legend = { display: false };
        opts.scales = {
            x: { grid: { color: GRID }, ticks: { ...TICK, maxRotation: 45, minRotation: 45 } },
            y: { grid: { color: GRID }, ticks: { ...TICK, callback: v => v.toLocaleString() } }
        };
        upsert('liveValue', 'liveValueChart', {
            type: 'line',
            data: {
                labels: [...valueHistory.labels],
                datasets: [{
                    label: 'Total Inventory Value ($)',
                    data: [...valueHistory.values],
                    borderColor: CONFIG.liveLine.border,
                    backgroundColor: CONFIG.liveLine.fill,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 3,
                    pointBackgroundColor: CONFIG.liveLine.border
                }]
            },
            options: opts
        });
    }

    function renderAllCharts() {
        const stats = DataManager.getStockStatistics();
        renderCategoryCountChart(DataManager.getCategorySummary(), stats.totalProducts);
        renderStockHealthChart(DataManager.getCategoryStockHealth());
        renderReorderChart(DataManager.getAttentionItems(CONFIG.attentionMaxItems));
        renderLiveValueChart(stats.totalValue);
    }

    return { renderAllCharts, renderCategoryCountChart, renderStockHealthChart, renderReorderChart, renderLiveValueChart };
})();
