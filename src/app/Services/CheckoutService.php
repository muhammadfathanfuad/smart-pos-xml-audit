<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        protected ProductRepository $productRepo,
        protected OrderRepository $orderRepo,
        protected XMLAuditService $xmlService
    ) {}

    public function processCheckout(array $cartItems, string $paymentMethod)
    {
        return DB::transaction(function () use ($cartItems, $paymentMethod) {
            $total = 0;
            $processedItems = [];

            // 1. Validasi & Hitung Total
            foreach ($cartItems as $item) {
                $product = $this->productRepo->findBySku($item['sku']);
                if (!$product || $product->stock < $item['qty']) {
                    throw new \Exception("Stok produk {$item['sku']} tidak mencukupi!");
                }
                
                $subtotal = $product->price * $item['qty'];
                $total += $subtotal;
                
                $processedItems[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'qty' => $item['qty'],
                    'price' => $product->price
                ];
            }

            // 2. Simpan Order
            $order = $this->orderRepo->createOrder([
                'invoice_number' => 'INV-' . strtoupper(Str::random(10)),
                'total_amount' => $total,
                'tax_amount' => $total * 0.11, // PPN 11%
                'payment_method' => $paymentMethod,
            ]);

            // 3. Simpan Order Items & Update Stok
            foreach ($processedItems as $pItem) {
                $order->items()->create([
                    'product_id' => $pItem['product_id'],
                    'qty' => $pItem['qty'],
                    'price_at_sale' => $pItem['price']
                ]);
                $this->productRepo->updateStock($pItem['product_id'], $pItem['qty']);
            }

            // 4. Generate XML Audit (Materi APL)
            $xmlContent = $this->xmlService->generateTransactionXML($order->toArray(), $processedItems);
            $hash = $this->xmlService->signXML($xmlContent);
            
            $this->orderRepo->createAuditSnapshot($order->id, $xmlContent, $hash);

            return $order;
        });
    }
}