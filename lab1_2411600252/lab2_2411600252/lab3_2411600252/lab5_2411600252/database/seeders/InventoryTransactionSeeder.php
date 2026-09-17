<?php

namespace Database\Seeders;

use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class InventoryTransactionSeeder extends Seeder
{
    /**
     * Adds a handful of realistic stock-out transactions on top of the
     * "initial inventory load" transactions created by ProductSeeder, so the
     * dashboard's "Recent Inventory Activity" table has mixed activity to show.
     */
    public function run(): void
    {
        $user = User::first();

        $samples = [
            ['sku' => 'ELE-1001', 'quantity' => 15, 'reference' => 'Customer order #8801'],
            ['sku' => 'ELE-1005', 'quantity' => 8, 'reference' => 'Customer order #8804'],
            ['sku' => 'OFF-3001', 'quantity' => 30, 'reference' => 'Customer order #8809'],
            ['sku' => 'CLO-4001', 'quantity' => 20, 'reference' => 'Customer order #8812'],
            ['sku' => 'ACC-5003', 'quantity' => 6, 'reference' => 'Customer order #8815'],
        ];

        foreach ($samples as $sample) {
            $product = Product::where('sku', $sample['sku'])->first();

            if (! $product) {
                continue;
            }

            // Skip if this exact transaction was already recorded on a previous seed run.
            $alreadyRecorded = $product->transactions()
                ->where('transaction_type', 'stock_out')
                ->where('reference', $sample['reference'])
                ->exists();

            if ($alreadyRecorded || $sample['quantity'] > $product->quantity) {
                continue; // skip duplicates or anything that would drive stock negative
            }

            InventoryTransaction::create([
                'product_id' => $product->id,
                'transaction_type' => 'stock_out',
                'quantity' => $sample['quantity'],
                'reference' => $sample['reference'],
                'user_id' => $user?->id,
            ]);

            $product->decrement('quantity', $sample['quantity']);
        }
    }
}
