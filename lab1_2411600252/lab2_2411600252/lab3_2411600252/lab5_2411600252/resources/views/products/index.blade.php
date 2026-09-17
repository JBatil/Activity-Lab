@extends('layouts.app')

@section('title', 'Inventory')

@section('content')
    <div class="d-flex justify-content-between flex-wrap align-items-center pb-2 mb-3 border-bottom">
        <h2>Inventory</h2>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Add Product
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('products.index') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label small text-muted mb-1">Search</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" id="search" class="form-control" placeholder="Search name or SKU..." value="{{ $filters['search'] }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label small text-muted mb-1">Category</label>
                    <select name="category" id="category" class="form-select form-select-sm">
                        <option value="all" {{ $filters['category'] === 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat }}" {{ $filters['category'] === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label small text-muted mb-1">Stock Status</label>
                    <select name="status" id="status" class="form-select form-select-sm">
                        <option value="all" {{ $filters['status'] === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="in_stock" {{ $filters['status'] === 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="low_stock" {{ $filters['status'] === 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out_of_stock" {{ $filters['status'] === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">Filter</button>
                    <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Inventory table -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Products ({{ $products->total() }})</h5>
            <a href="{{ route('products.export', request()->query()) }}" class="btn btn-sm btn-success">
                <i class="bi bi-download"></i> Export CSV
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Status</th>
                            <th>Supplier</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="{{ $product->isOutOfStock() ? 'table-danger' : ($product->isLowStock() ? 'table-warning' : '') }}">
                                <td>{{ $product->sku }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>${{ number_format($product->unit_price, 2) }}</td>
                                <td><span class="badge bg-{{ $product->stock_status_color }}">{{ $product->stock_status }}</span></td>
                                <td>{{ $product->supplier ?? '—' }}</td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('products.show', $product) }}" class="btn btn-outline-secondary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                              onsubmit="return confirm('Delete {{ $product->name }}? This cannot be undone.');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    No products match your filters. <a href="{{ route('products.index') }}">Clear filters</a>
                                    or <a href="{{ route('products.create') }}">add a new product</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $products->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endsection
