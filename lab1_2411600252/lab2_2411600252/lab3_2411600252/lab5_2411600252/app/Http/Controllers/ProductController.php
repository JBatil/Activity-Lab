<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the products, with search + filters.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            if ($category !== 'all') {
                $query->where('category', $category);
            }
        }

        if ($status = $request->input('status')) {
            if ($status === 'in_stock') {
                $query->inStock();
            } elseif ($status === 'low_stock') {
                $query->lowStock();
            } elseif ($status === 'out_of_stock') {
                $query->outOfStock();
            }
        }

        $products = $query->orderBy('name')->paginate(10)->withQueryString();

        $categories = Product::query()->select('category')->distinct()->orderBy('category')->pluck('category');

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'search' => $request->input('search', ''),
                'category' => $request->input('category', 'all'),
                'status' => $request->input('status', 'all'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Product::query()->select('category')->distinct()->orderBy('category')->pluck('category');

        return view('products.create', ['categories' => $categories]);
    }

    /**
     * Store a newly created product, plus an opening stock-in transaction.
     */
    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        $product = DB::transaction(function () use ($validated, $request) {
            $product = Product::create($validated);

            if ($product->quantity > 0) {
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'transaction_type' => 'stock_in',
                    'quantity' => $product->quantity,
                    'reference' => 'Initial stock on product creation',
                    'user_id' => $request->user()?->id,
                ]);
            }

            return $product;
        });

        return redirect()
            ->route('products.index')
            ->with('success', "Product \"{$product->name}\" was created successfully.");
    }

    /**
     * Display the specified product, with its recent stock movements.
     */
    public function show(Product $product)
    {
        $product->load(['transactions' => function ($q) {
            $q->with('user')->orderByDesc('created_at')->limit(15);
        }]);

        return view('products.show', ['product' => $product]);
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $categories = Product::query()->select('category')->distinct()->orderBy('category')->pluck('category');

        return view('products.edit', ['product' => $product, 'categories' => $categories]);
    }

    /**
     * Update the specified product. Any quantity change is recorded as an
     * inventory transaction so the movement history stays accurate.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();
        $originalQuantity = $product->quantity;

        DB::transaction(function () use ($product, $validated, $originalQuantity, $request) {
            $product->update($validated);

            $delta = $validated['quantity'] - $originalQuantity;

            if ($delta !== 0) {
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'transaction_type' => $delta > 0 ? 'stock_in' : 'stock_out',
                    'quantity' => abs($delta),
                    'reference' => 'Manual adjustment via product edit',
                    'user_id' => $request->user()?->id,
                ]);
            }
        });

        return redirect()
            ->route('products.index')
            ->with('success', "Product \"{$product->name}\" was updated successfully.");
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', "Product \"{$name}\" was deleted.");
    }

    /**
     * Record a stock-in or stock-out transaction for a product and keep
     * the product's quantity synchronized. Stock-out is rejected if it
     * would drive the quantity below zero.
     */
    public function adjustStock(Request $request, Product $product)
    {
        $data = $request->validate([
            'transaction_type' => ['required', 'in:stock_in,stock_out'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reference' => ['nullable', 'string', 'max:255'],
        ]);

        if ($data['transaction_type'] === 'stock_out' && $data['quantity'] > $product->quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'Stock-out quantity cannot exceed the current quantity on hand (' . $product->quantity . ').',
            ]);
        }

        DB::transaction(function () use ($product, $data, $request) {
            InventoryTransaction::create([
                'product_id' => $product->id,
                'transaction_type' => $data['transaction_type'],
                'quantity' => $data['quantity'],
                'reference' => $data['reference'] ?? null,
                'user_id' => $request->user()?->id,
            ]);

            $product->quantity = $data['transaction_type'] === 'stock_in'
                ? $product->quantity + $data['quantity']
                : $product->quantity - $data['quantity'];
            $product->save();
        });

        return redirect()
            ->route('products.show', $product)
            ->with('success', 'Inventory transaction recorded and stock level updated.');
    }

    /**
     * Export the (optionally filtered) product list as a CSV download.
     */
    public function export(Request $request): StreamedResponse
    {
        $query = Product::query();

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (($category = $request->input('category')) && $category !== 'all') {
            $query->where('category', $category);
        }

        $products = $query->orderBy('name')->get();

        $filename = 'inventory_export_' . now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['SKU', 'Name', 'Category', 'Quantity', 'Unit Price', 'Reorder Level', 'Status', 'Supplier']);

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->sku,
                    $product->name,
                    $product->category,
                    $product->quantity,
                    number_format((float) $product->unit_price, 2, '.', ''),
                    $product->reorder_level,
                    $product->stock_status,
                    $product->supplier,
                ]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
