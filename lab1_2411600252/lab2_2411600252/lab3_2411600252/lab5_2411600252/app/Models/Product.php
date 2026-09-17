<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes.
     */
    protected $fillable = [
        'name',
        'sku',
        'description',
        'category',
        'quantity',
        'reorder_level',
        'unit_price',
        'supplier',
    ];

    /**
     * Attribute casting.
     */
    protected $casts = [
        'quantity' => 'integer',
        'reorder_level' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    /**
     * A product has many inventory transactions (stock movements).
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    /**
     * Total value currently held for this product (quantity x unit price).
     */
    public function getInventoryValueAttribute(): float
    {
        return round($this->quantity * (float) $this->unit_price, 2);
    }

    /**
     * Business-logic helpers for stock status.
     */
    public function isOutOfStock(): bool
    {
        return $this->quantity <= 0;
    }

    public function isLowStock(): bool
    {
        return $this->quantity > 0 && $this->quantity <= $this->reorder_level;
    }

    public function isInStock(): bool
    {
        return $this->quantity > $this->reorder_level;
    }

    /**
     * Human readable stock status label, used in Blade views.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->isOutOfStock()) {
            return 'Out of Stock';
        }

        if ($this->isLowStock()) {
            return 'Low Stock';
        }

        return 'In Stock';
    }

    /**
     * Bootstrap badge color that matches the stock status.
     */
    public function getStockStatusColorAttribute(): string
    {
        return match ($this->stock_status) {
            'Out of Stock' => 'danger',
            'Low Stock' => 'warning',
            default => 'success',
        };
    }

    /**
     * Scope: products at or below reorder level (but not zero).
     */
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('quantity', '<=', 'reorder_level')->where('quantity', '>', 0);
    }

    /**
     * Scope: products with zero quantity.
     */
    public function scopeOutOfStock(Builder $query): Builder
    {
        return $query->where('quantity', '<=', 0);
    }

    /**
     * Scope: products that are comfortably above their reorder level.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->whereColumn('quantity', '>', 'reorder_level');
    }
}
