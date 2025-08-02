<?php

namespace Database\Seeders;

use App\Models\Stock;
use App\Models\Product;
use App\Models\ProductUnit;
use App\Models\ProductBrand;
use App\Models\ProductBundle;
use Illuminate\Database\Seeder;
use App\Models\ProductBundleItem;
use Database\Seeders\MasterSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker; // Import Faker

class ProductRelatedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $productBrands = ProductBrand::all();
        $productUnits = ProductUnit::where('name', 'pcs')->first();
        
        // Ensure master data exists before proceeding
        // if ($productBrands->isEmpty() || $productUnits->isEmpty()) {
        //     $this->call(MasterSeeder::class);
        //     $productBrands = ProductBrand::all();
        //     $productUnits = ProductUnit::all();
        // }
        
        // Product (buat 5 produk jika belum ada sama sekali)
        for ($i = 1; $i <= 5; $i++) {
            $product = Product::create([
                'product_brand_id' => $productBrands->random()->id,
                'name' => 'Produk ' . $i . ' ' . $faker->randomElement(['Basic', 'Pro', 'Max', 'Mini']),
                'minimum_selling_unit_id' => $productUnits->id,
                'selling_price' => $faker->numberBetween(10000, 500000), // Harga acak
                'image' => 'uploads/images/products/product-1.png', 
            ]);
            
            // Stock (random quantity for each product)
            Stock::create([
                'product_id' => $product->id,
                'quantity' => rand(50, 500), // Kuantitas stok acak
            ]);
        }
        
        $products = Product::all();
        $bundleItems = [];
        for ($i = 1; $i <= 2; $i++) {
            for ($item = 1; $item < rand(2, 5); $item++) {
                $bundleItems[] = $products->random();
            }

            $originalPrice = 0;
            foreach ($bundleItems as $item) {
                $originalPrice += $item->selling_price;
            }
            $specialBundlePrice = $originalPrice * $faker->randomFloat(2, 0.7, 0.95); // Diskon 5-30%

            $bundle = ProductBundle::create([
                'bundle_name' => 'Bundle ' . $i,
                'description' => $faker->paragraph(),
                'start_date' => $faker->dateTimeBetween('-6 months', 'now'),
                'end_date' => $faker->dateTimeBetween('now', '+6 months'),
                'special_bundle_price' => round($specialBundlePrice, 0),
                'original_price' => $originalPrice,
                'is_active' => $faker->boolean(80), // 80% kemungkinan aktif
            ]);

            foreach ($bundleItems as $item) {
                ProductBundleItem::create([
                    'product_bundle_id' => $bundle->id,
                    'product_id' => $item->id,
                    'quantity' => rand(1, 3),
                ]);
            }
        }
        // Menampilkan nama produk dari setiap item di $bundleItems
    }
}
