<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\SalesAgent;
use App\Models\ProductUnit;
use App\Models\ProductBundle;
use App\Models\PurchaseOrder;
use App\Models\DeliveryReturn;
use App\Models\StockAdjustment;
use Illuminate\Database\Seeder;
use App\Models\SalesTransaction;
use App\Models\SupplierPurchase;
use App\Models\WarehouseManager;
use App\Models\PurchaseOrderItem;
use Database\Seeders\AdminSeeder;
use App\Models\DeliveryReturnItem;
use Database\Seeders\MasterSeeder;
use Illuminate\Support\Facades\DB;
use App\Models\SalesTransactionItem;
use App\Models\SupplierPurchaseItem;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\SalesAgentSeeder;
use Database\Seeders\ProductRelatedSeeder;
use Faker\Factory as Faker; // Import Faker
use Database\Seeders\WarehouseManagerSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PurchaseAndSalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $admins = Admin::all();
        $salesAgents = SalesAgent::all();
        $customers = Customer::all();
        $products = Product::all();
        $suppliers = Supplier::all();
        $productUnits = ProductUnit::all();
        $warehouseManagers = WarehouseManager::all();
        $productBundles = ProductBundle::where('is_active', true)->get(); // Hanya bundle aktif

        DB::beginTransaction();
        try {
            // ============================ Purchase Orders (Customers) ============================
            // Simpan PO yang akan dikonfirmasi
            $confirmedPurchaseOrders = [];
            for ($i = 1; $i <= 5; $i++) {
                $orderDate = $faker->dateTimeBetween('this year - 6 months', 'now');
                $minDelivDate = (clone $orderDate)->modify('+3 days');
                $maxDelivDate = (clone $orderDate)->modify('+7 days');
                $deliveryDueDate = $faker->dateTimeBetween($minDelivDate, $maxDelivDate);

                $po = PurchaseOrder::create([
                    'customer_id' => $customers->random()->id,
                    'order_date' => $orderDate,
                    'delivery_date' => $deliveryDueDate,
                    'status' => 'confirmed',
                ]);
                $confirmedPurchaseOrders[] = $po; // Simpan PO yang dibuat

                // PurchaseOrderItem (optional, you can seed items here or separately)
                $itemCount = rand(1, 3);
                $orderedProducts = collect();
                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->whereNotIn('id', $orderedProducts->pluck('id'))->random();
                    $orderedProducts->push($product);

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $po->id,
                        'product_id' => $product->id,
                        'quantity' => rand(1, 5),
                    ]);
                }
            }

            // ============================ Sales Transactions ============================
            $successfulSalesCount = 0;
            foreach ($confirmedPurchaseOrders as $po) {
                // Cek apakah PO ini sudah punya SalesTransaction (untuk menghindari unique constraint)
                if (SalesTransaction::where('purchase_order_id', $po->id)->exists()) {
                    continue;
                }

                // Sales Transaction Date (invoice_date) harus setelah order_date PO
                $invoiceDate = $faker->dateTimeBetween($po->order_date, 'now');

                // Delivery confirmed at (bisa null kalau belum diantar/dibayar)
                // Sesuai alur, ini diisi saat pengantaran/pembayaran, jadi bisa null di awal
                $deliveryConfirmedAt = null;
                $paymentStatus = 'success'; // Default

                // Randomly simulate some deliveries being confirmed and paid
                if ($faker->boolean(80)) {
                    // 80% kemungkinan sudah dikonfirmasi dan dibayar
                    $deliveryConfirmedAt = (clone $invoiceDate)->modify('+' . rand(0, 7) . ' days'); // Bisa sampai 7 hari setelah invoice_date
                    $paymentStatus = $faker->randomElement(['process', 'success']);
                }

                // Ambil admin_user_id dan sales_agent_user_id dari model Admin dan SalesAgent
                // Ambil random admin, tapi batasi hanya dari 1 sampai 5 admin pertama
                $limitedAdmins = $admins->take(10);
                $limitedSales = $salesAgents->take(5);
                $adminUser = $limitedAdmins->random();
                $salesAgentUser = $limitedSales->random(); // Atau $po->salesAgent; jika relasi PO ke sales agent sudah di-load

                // Hitung initial_total_amount dan final_amount_paid
                $initialTotalAmount = 0;
                $salesTransactionItemsData = [];
                foreach ($confirmedPurchaseOrders as $poItem) {
                    $product = $poItem->purchase_order_items()->get();
                    foreach ($product as $poProduct) {
                        $product = $products->find($poProduct->product_id);
                        if ($product) {
                            $itemUnitPrice = $product->selling_price; // Harga jual per MSU
                            $itemInitialAmount = $poProduct->quantity * $itemUnitPrice;
                            $initialTotalAmount += $itemInitialAmount;

                            $quantitySold = $poProduct->quantity; // Default: semua terjual

                            // Simulate some returns on delivery
                            if ($paymentStatus != 'success' && $faker->boolean(30)) {
                                // 30% kemungkinan ada yg diretur sebagian jika belum full paid
                                // Jika ada retur di tempat, kurangi quantity_sold secara acak
                                $quantitySold = rand(1, $poItem->quantity - 1);
                                if ($quantitySold <= 0) {
                                    $quantitySold = 0;
                                } // Pastikan tidak negatif
                            }

                            $salesTransactionItemsData[] = [
                                'product_id' => $product->id,
                                'quantity_ordered' => $poProduct->quantity,
                                'quantity_sold' => $quantitySold,
                                'msu_price' => $itemUnitPrice,
                            ];
                        }
                    }
                }

                $discountPercent = $faker->numberBetween(0, 15);
                $totalAmountAfterDiscount = $initialTotalAmount * (1 - $discountPercent / 100);

                // final_amount_paid bisa kurang dari totalAmountAfterDiscount jika ada retur di tempat
                // atau jika paymentStatus adalah 'paid_partial'/'not_paid'
                $finalAmountPaid = $totalAmountAfterDiscount;
                if ($paymentStatus == 'success') {
                    $finalAmountPaid = $faker->randomFloat(2, 0, $totalAmountAfterDiscount * 0.9);
                } elseif ($paymentStatus == 'not_paid') {
                    $finalAmountPaid = 0;
                }
                try {
                    $salesTransaction = SalesTransaction::create([
                        'purchase_order_id' => $po->id,
                        'admin_id' => $adminUser->id, // Gunakan user_id
                        'sales_agent_id' => $salesAgentUser->id, // Gunakan user_id
                        'invoice_date' => $invoiceDate->format('Y-m-d'),
                        'discount_percent' => $discountPercent,
                        'initial_total_amount' => $initialTotalAmount,
                        'final_total_amount' => $finalAmountPaid,
                        'note' => "Lorem, ipsum dolor.",
                        'transaction_status' => $paymentStatus,
                        'delivery_confirmed_at' => $deliveryConfirmedAt,
                    ]);
                    $successfulSalesCount++;

                    // Tambahkan sales_transaction_id ke setiap item di $salesTransactionItemsData
                    foreach ($salesTransactionItemsData as $itemData) {
                        $itemData['sales_transaction_id'] = $salesTransaction->id;
                    }

                    // Tambahkan SalesTransactionItem
                    foreach ($salesTransactionItemsData as $itemData) {
                        $salesTransaction->sales_transaction_items()->create($itemData);
                    }

                    // Update stock based on quantity_sold
                    foreach ($salesTransactionItemsData as $itemData) {
                        $product = Product::find($itemData['product_id']);
                        if ($product && $itemData['quantity_sold'] > 0) {
                            $product->stock()->decrement('quantity', $itemData['quantity_sold']);
                        }
                    }

                    // If any item was "returned on delivery" (quantity_sold < quantity_ordered),
                    // we should create a DeliveryReturn entry and corresponding StockAdjustment.
                    // This logic might be complex to fully seed here, typically done in app flow.
                    // For seeding, we'll just reflect the final_amount_paid.
                    // You might need a separate seeder for DeliveryReturns if you want to populate them.
                } catch (\Exception $e) {
                    $this->command->error('Error creating Sales Transaction for PO ' . $po->id . ': ' . $e->getMessage());
                }
            }
            $this->command->info('Created ' . $successfulSalesCount . ' Sales Transactions.');

            // ============================ Supplier Purchases ============================
            $numSupplierPurchases = rand(3, 8);
            $successfulSupplierPurchases = 0;
            for ($i = 0; $i < $numSupplierPurchases; $i++) {
                $purchaseDate = $faker->dateTimeBetween('this year - 6 months', 'now');
                $adminUser = $admins->random();
                $supplier = $suppliers->random();

                $supplierPurchase = SupplierPurchase::create([
                    'admin_id' => $adminUser->id,
                    'supplier_id' => $supplier->id,
                    'purchase_date' => $purchaseDate->format('Y-m-d'),
                    'invoice_number' => 'INV-SUP-' . $faker->unique()->randomNumber(5),
                    'total_amount' => 0, // Will be calculated by items
                    'notes' => $faker->sentence(),
                ]);
                $successfulSupplierPurchases++;

                $itemCount = rand(1, 4);
                $totalAmount = 0;
                $purchasedProducts = collect();

                for ($j = 0; $j < $itemCount; $j++) {
                    $product = $products->whereNotIn('id', $purchasedProducts->pluck('id'))->random();
                    $purchasedProducts->push($product);

                    $quantity = rand(10, 200);
                    // Ambil unit acak yang bukan MSU dari produk, atau MSU jika tidak ada konversi
                    $possibleUnits = $productUnits->where('id', '!=', $product->minimum_selling_unit_id);
                    $selectedUnit = $possibleUnits->isNotEmpty() ? $possibleUnits->random() : $productUnits->firstWhere('id', $product->minimum_selling_unit_id);

                    // Harga beli per unit yang dipesan
                    $unitPurchasePrice = $product->selling_price * $faker->randomFloat(2, 0.5, 0.8);

                    $supplierPurchase->supplier_purchase_item()->create([
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'product_unit_id' => $selectedUnit->id, // Gunakan unit_id dari ProductUnit
                        'product_unit_price' => round($unitPurchasePrice, 2),
                    ]);
                    $totalAmount += $quantity * $unitPurchasePrice;
                }
                $supplierPurchase->update(['total_amount' => round($totalAmount, 2)]);
            }
            $this->command->info('Created ' . $successfulSupplierPurchases . ' Supplier Purchases.');

            // ============================ Delivery Returns ============================
            $successfulDeliveryReturns = 0;
            if (!empty($salesTransactionsWithPotentialReturns)) {
                $numDeliveryReturns = rand(1, min(5, count($salesTransactionsWithPotentialReturns))); // max 5 returns for now

                for ($i = 0; $i < $numDeliveryReturns; $i++) {
                    $salesTransaction = $faker->randomElement($salesTransactionsWithPotentialReturns);

                    // Only create a return if it hasn't been returned yet (avoid unique constraint for demo)
                    if (DeliveryReturn::where('sales_transaction_id', $salesTransaction->id)->exists()) {
                        continue;
                    }

                    $returnDate = (clone $salesTransaction->delivery_confirmed_at ?? $salesTransaction->invoice_date)->addDays(rand(0, 5)); // Return happens shortly after delivery/invoice
                    if ($returnDate->isAfter(Carbon::now())) {
                        continue; // Don't create future returns
                    }

                    $deliveryReturn = DeliveryReturn::create([
                        'sales_transaction_id' => $salesTransaction->id,
                        'sales_agent_user_id' => $salesTransaction->sales_agent_user_id, // Sales agent yang mengantar
                        'return_date' => $returnDate->format('Y-m-d'),
                        'reason' => $faker->randomElement(['Barang tidak diambil', 'Ukuran salah dari pesanan', 'Barang rusak saat pengantaran']),
                        'status' => $faker->randomElement(['pending_admin_confirmation', 'confirmed_by_admin']), // Langsung ke status awal atau dikonfirmasi
                        'confirmed_by_admin_user_id' => $faker->boolean(70) ? $admins->random()->id : null, // 70% chance to be confirmed by an admin
                        'confirmed_at' => $faker->boolean(70) ? (clone $returnDate)->addDays(rand(0, 2)) : null,
                    ]);
                    $successfulDeliveryReturns++;

                    // DeliveryReturnItem - Only return items that were *not* sold in SalesTransactionItem
                    $notSoldItems = $salesTransaction->items->filter(function ($item) {
                        return $item->quantity_sold < $item->quantity_ordered;
                    });

                    if ($notSoldItems->isNotEmpty()) {
                        $itemToReturn = $notSoldItems->random(); // Randomly pick one item that wasn't fully sold

                        $quantityReturned = $itemToReturn->quantity_ordered - $itemToReturn->quantity_sold;
                        $quantityReturned = rand(1, max(1, $quantityReturned)); // Ensure at least 1 returned, max what wasn't sold

                        $deliveryReturn->items()->create([
                            'product_id' => $itemToReturn->product_id,
                            'quantity_returned' => $quantityReturned, // Quantity in MSU
                        ]);

                        // Simulate Stock Adjustment for returned items by Warehouse Manager
                        if ($deliveryReturn->status === 'confirmed_by_admin') {
                            $warehouseManager = $admins->random()->warehouseManager ?? $faker->randomElement($warehouseManagers); // Assume admin can also be WM or pick random WM
                            if ($warehouseManager) {
                                StockAdjustment::create([
                                    'warehouse_manager_id' => $warehouseManager->id,
                                    'product_id' => $itemToReturn->product_id,
                                    'reason' => 'Pengembalian dari Pengantaran Transaksi #' . $salesTransaction->id,
                                    'quantity' => $quantityReturned,
                                    'adjustment_type' => 'increase',
                                    'source_type' => 'delivery_return',
                                    'source_id' => $deliveryReturn->id,
                                    'adjustment_date' => Carbon::parse($deliveryReturn->confirmed_at ?? $deliveryReturn->return_date)->format('Y-m-d'),
                                ]);
                                // Update actual stock
                                $stock = Stock::where('product_id', $itemToReturn->product_id)->first();
                                if ($stock) {
                                    $stock->increment('quantity', $quantityReturned);
                                } else {
                                    Stock::create(['product_id' => $itemToReturn->product_id, 'quantity' => $quantityReturned]);
                                }
                            }
                        }
                    }
                }
            }
            $this->command->info('Created ' . $successfulDeliveryReturns . ' Delivery Returns.');

            // ============================ Stock Adjustments (Manual/Other Reasons) ============================
            $numManualAdjustments = rand(5, 10);
            $successfulManualAdjustments = 0;
            $warehouseManagers = \App\Models\WarehouseManager::all(); // Ensure this is loaded if not already at the top

            if ($warehouseManagers->isNotEmpty()) {
                for ($i = 0; $i < $numManualAdjustments; $i++) {
                    $product = $products->random();
                    $warehouseManager = $warehouseManagers->random();
                    $adjustmentDate = $faker->dateTimeBetween('this year - 6 months', 'now');

                    $adjustmentType = $faker->randomElement(['increase', 'decrease']);
                    $quantity = rand(1, 20); // Random quantity for adjustment

                    // Adjust quantity sign based on type
                    $quantityAdjusted = $adjustmentType == 'decrease' ? -$quantity : $quantity;

                    StockAdjustment::create([
                        'warehouse_manager_id' => $warehouseManager->id,
                        'product_id' => $product->id,
                        'reason' => $faker->randomElement(['Barang rusak di gudang', 'Selisih stok fisik', 'Barang hilang', 'Penerimaan non-pembelian']),
                        'quantity' => $quantityAdjusted,
                        'adjustment_type' => $adjustmentType,
                        'source_type' => 'physical_check', // Or 'other'
                        'source_id' => null,
                        'adjustment_date' => $adjustmentDate->format('Y-m-d'),
                    ]);
                    $successfulManualAdjustments++;

                    // Update actual stock for manual adjustments
                    $stock = Stock::where('product_id', $product->id)->first();
                    if ($stock) {
                        $stock->increment('quantity', $quantityAdjusted);
                    } else {
                        // If no stock record, create one
                        Stock::create(['product_id' => $product->id, 'quantity' => $quantityAdjusted]);
                    }
                }
            }
            $this->command->info('Created ' . $successfulManualAdjustments . ' manual Stock Adjustments.');
            DB::commit(); // Commit all transactions if successful
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback on error
            $this->command->error('Seeding failed: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
        }
    }
}
