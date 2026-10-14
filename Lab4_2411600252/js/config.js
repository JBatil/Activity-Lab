const CONFIG = {
    dataUrl: 'data/products.json',
    simulationIntervalMs: 8000, 
    statusColors: {
        'In Stock': '#10B981',
        'Low Stock': '#f3a712',
        'Out of Stock': '#dc3545'
    },
    chartPalette: [
        '#4F46E5', '#6366F1', '#10B981', '#f3a712',
        '#17a2b8', '#dc3545', '#0EA5E9', '#8B5CF6'
    ],
    // Chart colors based on the site theme (indigo / sky / violet / amber)
    categoryPalette: [
        '#4F46E5', '#0EA5E9', '#8B5CF6', '#f3a712',
        '#17a2b8', '#6366F1', '#dc3545', '#10B981'
    ],
    // Same status colors used by the table badges
    stockHealthColors: {
        'In Stock': '#10B981',
        'Low Stock': '#f3a712',
        'Out of Stock': '#dc3545'
    },
    attentionColors: { quantity: '#f3a712', reorder: '#4F46E5' },
    liveLine: { border: '#4F46E5', fill: 'rgba(79, 70, 229, 0.12)' },
    liveHistoryMaxPoints: 20,
    attentionMaxItems: 8
};

function getStockStatus(product) {
    if (product.quantity <= 0) return 'Out of Stock';
    if (product.quantity <= product.reorder_level) return 'Low Stock';
    return 'In Stock';
}