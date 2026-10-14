const DataManager = (function () {
    let allProducts = [];      
    let activeFilters = {
        category: 'all',
        stockStatus: 'all',
        minPrice: null,
        maxPrice: null,
        searchQuery: ''
    };

    let usingFallback = false;

    // Built-in copy of data/products.json, used when the JSON file cannot be loaded
    // (e.g. page opened with file://, file missing, or server not running)
    const FALLBACK_PRODUCTS = [
        { id: 1, sku: "ELE-1001", name: "Wireless Mouse", category: "Electronics", quantity: 84, unit_price: 19.99, reorder_level: 20, supplier: "TechSource Inc." },
        { id: 2, sku: "ELE-1002", name: "USB-C Charging Cable", category: "Electronics", quantity: 12, unit_price: 9.5, reorder_level: 25, supplier: "TechSource Inc." },
        { id: 3, sku: "ELE-1003", name: "Bluetooth Headphones", category: "Electronics", quantity: 0, unit_price: 59.99, reorder_level: 15, supplier: "AudioWorks" },
        { id: 4, sku: "ELE-1004", name: "27\" Monitor", category: "Electronics", quantity: 18, unit_price: 189.0, reorder_level: 10, supplier: "DisplayTech" },
        { id: 5, sku: "ELE-1005", name: "Mechanical Keyboard", category: "Electronics", quantity: 45, unit_price: 74.99, reorder_level: 15, supplier: "TechSource Inc." },
        { id: 6, sku: "ELE-1006", name: "Webcam 1080p", category: "Electronics", quantity: 6, unit_price: 34.99, reorder_level: 12, supplier: "AudioWorks" },
        { id: 7, sku: "FUR-2001", name: "Ergonomic Office Chair", category: "Furniture", quantity: 22, unit_price: 149.0, reorder_level: 8, supplier: "ComfortSeating Co." },
        { id: 8, sku: "FUR-2002", name: "Standing Desk", category: "Furniture", quantity: 9, unit_price: 299.0, reorder_level: 5, supplier: "ComfortSeating Co." },
        { id: 9, sku: "FUR-2003", name: "Bookshelf", category: "Furniture", quantity: 0, unit_price: 89.5, reorder_level: 6, supplier: "HomeCraft" },
        { id: 10, sku: "FUR-2004", name: "Filing Cabinet", category: "Furniture", quantity: 14, unit_price: 120.0, reorder_level: 5, supplier: "HomeCraft" },
        { id: 11, sku: "OFF-3001", name: "A4 Paper Ream (500 sheets)", category: "Office Supplies", quantity: 210, unit_price: 4.25, reorder_level: 50, supplier: "PaperPlus" },
        { id: 12, sku: "OFF-3002", name: "Ballpoint Pens (Box of 12)", category: "Office Supplies", quantity: 8, unit_price: 3.1, reorder_level: 20, supplier: "PaperPlus" },
        { id: 13, sku: "OFF-3003", name: "Sticky Notes Pack", category: "Office Supplies", quantity: 65, unit_price: 2.5, reorder_level: 15, supplier: "PaperPlus" },
        { id: 14, sku: "OFF-3004", name: "Stapler Heavy Duty", category: "Office Supplies", quantity: 3, unit_price: 12.75, reorder_level: 10, supplier: "OfficeMate" },
        { id: 15, sku: "OFF-3005", name: "Whiteboard Markers (Set of 8)", category: "Office Supplies", quantity: 0, unit_price: 8.4, reorder_level: 12, supplier: "OfficeMate" },
        { id: 16, sku: "CLO-4001", name: "Cotton T-Shirt", category: "Clothing", quantity: 130, unit_price: 14.99, reorder_level: 30, supplier: "ThreadLine" },
        { id: 17, sku: "CLO-4002", name: "Denim Jacket", category: "Clothing", quantity: 19, unit_price: 54.0, reorder_level: 10, supplier: "ThreadLine" },
        { id: 18, sku: "CLO-4003", name: "Running Shoes", category: "Clothing", quantity: 7, unit_price: 79.99, reorder_level: 15, supplier: "StrideCo" },
        { id: 19, sku: "CLO-4004", name: "Wool Beanie", category: "Clothing", quantity: 0, unit_price: 12.5, reorder_level: 20, supplier: "ThreadLine" },
        { id: 20, sku: "ACC-5001", name: "Leather Wallet", category: "Accessories", quantity: 41, unit_price: 29.99, reorder_level: 15, supplier: "StyleCraft" },
        { id: 21, sku: "ACC-5002", name: "Sunglasses", category: "Accessories", quantity: 5, unit_price: 24.99, reorder_level: 10, supplier: "StyleCraft" },
        { id: 22, sku: "ACC-5003", name: "Canvas Backpack", category: "Accessories", quantity: 33, unit_price: 44.5, reorder_level: 10, supplier: "StyleCraft" },
        { id: 23, sku: "ACC-5004", name: "Stainless Water Bottle", category: "Accessories", quantity: 2, unit_price: 16.0, reorder_level: 20, supplier: "HydroGear" },
        { id: 24, sku: "ACC-5005", name: "Travel Duffel Bag", category: "Accessories", quantity: 27, unit_price: 39.99, reorder_level: 8, supplier: "HydroGear" },
        { id: 25, sku: "ELE-1007", name: "Portable SSD 1TB", category: "Electronics", quantity: 11, unit_price: 89.0, reorder_level: 10, supplier: "DisplayTech" }
    ];

    async function initializeData() {
        let rawProducts;
        try {
            const response = await fetch(CONFIG.dataUrl);
            if (!response.ok) {
                throw new Error(`Failed to load product data (status ${response.status})`);
            }
            rawProducts = await response.json();
            if (!Array.isArray(rawProducts) || rawProducts.length === 0) {
                throw new Error('Product data is empty or invalid');
            }
            usingFallback = false;
        } catch (error) {
            console.warn('Could not load JSON data, using built-in fallback data:', error);
            rawProducts = FALLBACK_PRODUCTS.map(p => ({ ...p }));
            usingFallback = true;
        }
        allProducts = rawProducts.map(p => ({ ...p, status: getStockStatus(p) }));
        return allProducts;
    }

    function isUsingFallback() {
        return usingFallback;
    }

    function getProducts() {
        return allProducts;
    }

    function getProductById(id) {
        return allProducts.find(p => p.id === Number(id)) || null;
    }

    function getProductsByCategory(category) {
        if (category === 'all') return allProducts;
        return allProducts.filter(p => p.category === category);
    }

    function getLowStockProducts() {
        return allProducts.filter(p => p.status === 'Low Stock' || p.status === 'Out of Stock');
    }

    function getStockStatistics() {
        const totalProducts = allProducts.length;
        const totalValue = allProducts.reduce((sum, p) => sum + p.quantity * p.unit_price, 0);
        const lowStockCount = allProducts.filter(p => p.status === 'Low Stock').length;
        const outOfStockCount = allProducts.filter(p => p.status === 'Out of Stock').length;
        const totalUnits = allProducts.reduce((sum, p) => sum + p.quantity, 0);

        return { totalProducts, totalValue, lowStockCount, outOfStockCount, totalUnits };
    }

    function getCategorySummary() {
        const summary = {};
        allProducts.forEach(p => {
            if (!summary[p.category]) {
                summary[p.category] = { category: p.category, totalValue: 0, totalQuantity: 0, productCount: 0 };
            }
            summary[p.category].totalValue += p.quantity * p.unit_price;
            summary[p.category].totalQuantity += p.quantity;
            summary[p.category].productCount += 1;
        });
        return Object.values(summary);
    }

    function getTopProductsByValue(limit = 5) {
        return [...allProducts]
            .sort((a, b) => (b.quantity * b.unit_price) - (a.quantity * a.unit_price))
            .slice(0, limit);
    }

    function getCategoryStockHealth() {
        const map = {};
        allProducts.forEach(p => {
            if (!map[p.category]) map[p.category] = { category: p.category, total: 0, 'In Stock': 0, 'Low Stock': 0, 'Out of Stock': 0 };
            map[p.category].total += 1;
            map[p.category][p.status] += 1;
        });
        return Object.values(map)
            .sort((a, b) => a.category.localeCompare(b.category))
            .map(c => ({
                category: c.category,
                inStockPct: (c['In Stock'] / c.total) * 100,
                lowStockPct: (c['Low Stock'] / c.total) * 100,
                outOfStockPct: (c['Out of Stock'] / c.total) * 100
            }));
    }

    function getAttentionItems(limit = 8) {
        return allProducts
            .filter(p => p.quantity <= p.reorder_level)
            .sort((a, b) => (b.reorder_level - b.quantity) - (a.reorder_level - a.quantity))
            .slice(0, limit);
    }

    function getCategories() {
        return [...new Set(allProducts.map(p => p.category))].sort();
    }

    function filterByCategory(category) {
        activeFilters.category = category;
        return applyFilters();
    }

    function filterByStockStatus(status) {
        activeFilters.stockStatus = status;
        return applyFilters();
    }

    function filterByPriceRange(min, max) {
        activeFilters.minPrice = min;
        activeFilters.maxPrice = max;
        return applyFilters();
    }

    function setSearchQuery(query) {
        activeFilters.searchQuery = (query || '').trim().toLowerCase();
        return applyFilters();
    }

    function resetFilters() {
        activeFilters = { category: 'all', stockStatus: 'all', minPrice: null, maxPrice: null, searchQuery: '' };
        return applyFilters();
    }

    function getActiveFilters() {
        return { ...activeFilters };
    }

    function applyFilters() {
        let result = allProducts;

        if (activeFilters.category !== 'all') {
            result = result.filter(p => p.category === activeFilters.category);
        }
        if (activeFilters.stockStatus !== 'all') {
            result = result.filter(p => p.status === activeFilters.stockStatus);
        }
        if (activeFilters.minPrice !== null && !isNaN(activeFilters.minPrice)) {
            result = result.filter(p => p.unit_price >= activeFilters.minPrice);
        }
        if (activeFilters.maxPrice !== null && !isNaN(activeFilters.maxPrice)) {
            result = result.filter(p => p.unit_price <= activeFilters.maxPrice);
        }
        if (activeFilters.searchQuery) {
            const q = activeFilters.searchQuery;
            result = result.filter(p =>
                p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q)
            );
        }
        return result;
    }

    function searchProducts(query) {
        const q = (query || '').trim().toLowerCase();
        if (!q) return allProducts;
        return allProducts.filter(p =>
            p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q)
        );
    }

    function simulateInventoryChange() {
        if (allProducts.length === 0) return null;
        const index = Math.floor(Math.random() * allProducts.length);
        const product = allProducts[index];
        const delta = Math.floor(Math.random() * 11) - 5; // -5..+5
        product.quantity = Math.max(0, product.quantity + delta);
        product.status = getStockStatus(product);
        return { product, delta };
    }

    function exportToCSV(data) {
        const rows = data && data.length ? data : allProducts;
        if (!rows.length) return '';

        const headers = ['SKU', 'Name', 'Category', 'Quantity', 'Unit Price', 'Reorder Level', 'Status', 'Supplier'];
        const lines = [headers.join(',')];

        rows.forEach(p => {
            const line = [
                p.sku,
                `"${p.name.replace(/"/g, '""')}"`,
                p.category,
                p.quantity,
                p.unit_price.toFixed(2),
                p.reorder_level,
                p.status,
                `"${p.supplier.replace(/"/g, '""')}"`
            ].join(',');
            lines.push(line);
        });

        return lines.join('\n');
    }

    function downloadCSV(csvContent, filename) {
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', filename || 'inventory_export.csv');
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }

    return {
        initializeData,
        isUsingFallback,
        getProducts,
        getProductById,
        getProductsByCategory,
        getLowStockProducts,
        getStockStatistics,
        getCategorySummary,
        getCategoryStockHealth,
        getAttentionItems,
        getTopProductsByValue,
        getCategories,
        filterByCategory,
        filterByStockStatus,
        filterByPriceRange,
        setSearchQuery,
        resetFilters,
        getActiveFilters,
        applyFilters,
        searchProducts,
        simulateInventoryChange,
        exportToCSV,
        downloadCSV
    };
})();