@csrf

<div class="row g-3">
    <div class="col-md-6">
        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $product->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
        <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror"
               value="{{ old('sku', $product->sku ?? '') }}" required>
        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description ?? '') }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
        <input type="text" name="category" id="category" list="categoryList"
               class="form-control @error('category') is-invalid @enderror"
               value="{{ old('category', $product->category ?? '') }}" required>
        <datalist id="categoryList">
            @foreach (($categories ?? []) as $cat)
                <option value="{{ $cat }}">
            @endforeach
            <option value="Electronics">
            <option value="Furniture">
            <option value="Office Supplies">
            <option value="Clothing">
            <option value="Accessories">
        </datalist>
        @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="supplier" class="form-label">Supplier</label>
        <input type="text" name="supplier" id="supplier" class="form-control @error('supplier') is-invalid @enderror"
               value="{{ old('supplier', $product->supplier ?? '') }}">
        @error('supplier') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label for="unit_price" class="form-label">Unit Price ($) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" min="0" name="unit_price" id="unit_price"
               class="form-control @error('unit_price') is-invalid @enderror"
               value="{{ old('unit_price', $product->unit_price ?? '') }}" required>
        @error('unit_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="quantity" class="form-label">Quantity on Hand <span class="text-danger">*</span></label>
        <input type="number" min="0" name="quantity" id="quantity"
               class="form-control @error('quantity') is-invalid @enderror"
               value="{{ old('quantity', $product->quantity ?? 0) }}" required>
        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="reorder_level" class="form-label">Reorder Level <span class="text-danger">*</span></label>
        <input type="number" min="0" name="reorder_level" id="reorder_level"
               class="form-control @error('reorder_level') is-invalid @enderror"
               value="{{ old('reorder_level', $product->reorder_level ?? 0) }}" required>
        <div class="form-text">Product is flagged "Low Stock" once quantity falls to this level or below.</div>
        @error('reorder_level') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mt-4 d-flex gap-2">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> {{ isset($product) ? 'Update Product' : 'Save Product' }}
    </button>
    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Cancel</a>
</div>
