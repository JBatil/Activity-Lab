<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'transaction_type',
        'quantity',
        'reference',
        'user_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * A transaction belongs to a single product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * A transaction is recorded by (attributed to) a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Human readable label for the transaction type.
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->transaction_type === 'stock_in' ? 'Stock In' : 'Stock Out';
    }
}
