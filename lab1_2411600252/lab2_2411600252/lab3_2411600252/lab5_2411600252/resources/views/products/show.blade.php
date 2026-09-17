@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="d-flex justify-content-between flex-wrap align-items-center pb-2 mb-3 border-bottom">
        <h2>{{ $product->name }}</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <form method="POST" action="{{ route('products.destroy', $product) }}"
                  onsubmit="return confirm('Delete {{ $product->name }}? This cannot be undone.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i> Delete</button>
            </form>
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Product Details</h5>
                    <span class="badge bg-{{ $product->stock_status_color }} fs-6">{{ $product->stock_status }}</span>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">SKU</dt><dd class="col-sm-8">{{ $product->sku }}</dd>
                        <dt class="col-sm-4">Category</dt><dd class="col-sm-8">{{ $product->category }}</dd>
                        <dt class="col-sm-4">Supplier</dt><dd class="col-sm-8">{{ $product->supplier ?? '—' }}</dd>
                        <dt class="col-sm-4">Description</dt><dd class="col-sm-8">{{ $product->description ?? '—' }}</dd>
                        <dt class="col-sm-4">Quantity on Hand</dt><dd class="col-sm-8">{{ $product->quantity }}</dd>
                        <dt class="col-sm-4">Reorder Level</dt><dd class="col-sm-8">{{ $product->reorder_level }}</dd>
                        <dt class="col-sm-4">Unit Price</dt><dd class="col-sm-8">${{ number_format($product->unit_price, 2) }}</dd>
                        <dt class="col-sm-4">Inventory Value</dt><dd class="col-sm-8">${{ number_format($product->inventory_value, 2) }}</dd>
                        <dt class="col-sm-4">Last Updated</dt><dd class="col-sm-8">{{ $product->updated_at->format('M j, Y g:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-header"><h5 class="mb-0">Record Stock Movement</h5></div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('products.stock', $product) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select name="transaction_type" class="form-select" required>
                                <option value="stock_in">Stock In</option>
                                <option value="stock_out">Stock Out</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" name="quantity" min="1" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reference (optional)</label>
                            <input type="text" name="reference" class="form-control" placeholder="e.g. PO-2026-0143">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-arrow-repeat"></i> Record Transaction
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h5 class="mb-0">Recent Inventory Transactions</h5></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Date</th><th>Type</th><th>Quantity</th><th>Reference</th><th>User</th></tr></thead>
                    <tbody>
                        @forelse ($product->transactions as $tx)
                            <tr>
                                <td>{{ $tx->created_at->format('M j, Y g:i A') }}</td>
                                <td>
                                    <span class="badge bg-{{ $tx->transaction_type === 'stock_in' ? 'success' : 'secondary' }}">
                                        {{ $tx->type_label }}
                                    </span>
                                </td>
                                <td>{{ $tx->quantity }}</td>
                                <td>{{ $tx->reference ?? '—' }}</td>
                                <td>{{ $tx->user->name ?? 'System' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-3">No inventory transactions recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
