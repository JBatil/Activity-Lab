<?php

namespace Database\Seeders;

use App\Models\InventoryTransaction;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Product data supplied directly by the user (e-commerce catalog).
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Wireless Mouse', 'sku' => 'ELE-1001', 'category' => 'Electronics', 'quantity' => 84, 'reorder_level' => 20, 'unit_price' => 19.99, 'supplier' => 'TechSource Inc.'],
            ['name' => 'USB-C Charging Cable', 'sku' => 'ELE-1002', 'category' => 'Electronics', 'quantity' => 12, 'reorder_level' => 25, 'unit_price' => 9.50, 'supplier' => 'TechSource Inc.'],
            ['name' => 'Bluetooth Headphones', 'sku' => 'ELE-1003', 'category' => 'Electronics', 'quantity' => 0, 'reorder_level' => 15, 'unit_price' => 59.99, 'supplier' => 'AudioWorks'],
            ['name' => '27" Monitor', 'sku' => 'ELE-1004', 'category' => 'Electronics', 'quantity' => 18, 'reorder_level' => 10, 'unit_price' => 189.00, 'supplier' => 'DisplayTech'],
            ['name' => 'Mechanical Keyboard', 'sku' => 'ELE-1005', 'category' => 'Electronics', 'quantity' => 45, 'reorder_level' => 15, 'unit_price' => 74.99, 'supplier' => 'TechSource Inc.'],
            ['name' => 'Webcam 1080p', 'sku' => 'ELE-1006', 'category' => 'Electronics', 'quantity' => 6, 'reorder_level' => 12, 'unit_price' => 34.99, 'supplier' => 'AudioWorks'],
            ['name' => 'Ergonomic Office Chair', 'sku' => 'FUR-2001', 'category' => 'Furniture', 'quantity' => 22, 'reorder_level' => 8, 'unit_price' => 149.00, 'supplier' => 'ComfortSeating Co.'],
            ['name' => 'Standing Desk', 'sku' => 'FUR-2002', 'category' => 'Furniture', 'quantity' => 9, 'reorder_level' => 5, 'unit_price' => 299.00, 'supplier' => 'ComfortSeating Co.'],
            ['name' => 'Bookshelf', 'sku' => 'FUR-2003', 'category' => 'Furniture', 'quantity' => 0, 'reorder_level' => 6, 'unit_price' => 89.50, 'supplier' => 'HomeCraft'],
            ['name' => 'Filing Cabinet', 'sku' => 'FUR-2004', 'category' => 'Furniture', 'quantity' => 14, 'reorder_level' => 5, 'unit_price' => 120.00, 'supplier' => 'HomeCraft'],
            ['name' => 'A4 Paper Ream (500 sheets)', 'sku' => 'OFF-3001', 'category' => 'Office Supplies', 'quantity' => 210, 'reorder_level' => 50, 'unit_price' => 4.25, 'supplier' => 'PaperPlus'],
            ['name' => 'Ballpoint Pens (Box of 12)', 'sku' => 'OFF-3002', 'category' => 'Office Supplies', 'quantity' => 8, 'reorder_level' => 20, 'unit_price' => 3.10, 'supplier' => 'PaperPlus'],
            ['name' => 'Sticky Notes Pack', 'sku' => 'OFF-3003', 'category' => 'Office Supplies', 'quantity' => 65, 'reorder_level' => 15, 'unit_price' => 2.50, 'supplier' => 'PaperPlus'],
            ['name' => 'Stapler Heavy Duty', 'sku' => 'OFF-3004', 'category' => 'Office Supplies', 'quantity' => 3, 'reorder_level' => 10, 'unit_price' => 12.75, 'supplier' => 'OfficeMate'],
            ['name' => 'Whiteboard Markers (Set of 8)', 'sku' => 'OFF-3005', 'category' => 'Office Supplies', 'quantity' => 0, 'reorder_level' => 12, 'unit_price' => 8.40, 'supplier' => 'OfficeMate'],
            ['name' => 'Cotton T-Shirt', 'sku' => 'CLO-4001', 'category' => 'Clothing', 'quantity' => 130, 'reorder_level' => 30, 'unit_price' => 14.99, 'supplier' => 'ThreadLine'],
            ['name' => 'Denim Jacket', 'sku' => 'CLO-4002', 'category' => 'Clothing', 'quantity' => 19, 'reorder_level' => 10, 'unit_price' => 54.00, 'supplier' => 'ThreadLine'],
            ['name' => 'Running Shoes', 'sku' => 'CLO-4003', 'category' => 'Clothing', 'quantity' => 7, 'reorder_level' => 15, 'unit_price' => 79.99, 'supplier' => 'StrideCo'],
            ['name' => 'Wool Beanie', 'sku' => 'CLO-4004', 'category' => 'Clothing', 'quantity' => 0, 'reorder_level' => 20, 'unit_price' => 12.50, 'supplier' => 'ThreadLine'],
            ['name' => 'Leather Wallet', 'sku' => 'ACC-5001', 'category' => 'Accessories', 'quantity' => 41, 'reorder_level' => 15, 'unit_price' => 29.99, 'supplier' => 'StyleCraft'],
            ['name' => 'Sunglasses', 'sku' => 'ACC-5002', 'category' => 'Accessories', 'quantity' => 5, 'reorder_level' => 10, 'unit_price' => 24.99, 'supplier' => 'StyleCraft'],
            ['name' => 'Canvas Backpack', 'sku' => 'ACC-5003', 'category' => 'Accessories', 'quantity' => 33, 'reorder_level' => 10, 'unit_price' => 44.50, 'supplier' => 'StyleCraft'],
            ['name' => 'Stainless Water Bottle', 'sku' => 'ACC-5004', 'category' => 'Accessories', 'quantity' => 2, 'reorder_level' => 20, 'unit_price' => 16.00, 'supplier' => 'HydroGear'],
            ['name' => 'Travel Duffel Bag', 'sku' => 'ACC-5005', 'category' => 'Accessories', 'quantity' => 27, 'reorder_level' => 8, 'unit_price' => 39.99, 'supplier' => 'HydroGear'],
            ['name' => 'Portable SSD 1TB', 'sku' => 'ELE-1007', 'category' => 'Electronics', 'quantity' => 11, 'reorder_level' => 10, 'unit_price' => 89.00, 'supplier' => 'DisplayTech'],
        ];

        foreach ($products as $data) {
            $product = Product::updateOrCreate(
                ['sku' => $data['sku']],
                $data
            );

            // Record an opening stock-in transaction only the first time this
            // product is seeded (avoids duplicate transactions on re-seeding).
            if ($product->quantity > 0 && $product->transactions()->count() === 0) {
                InventoryTransaction::create([
                    'product_id' => $product->id,
                    'transaction_type' => 'stock_in',
                    'quantity' => $product->quantity,
                    'reference' => 'Initial inventory load',
                    'user_id' => null,
                ]);
            }
        }
    }
}
