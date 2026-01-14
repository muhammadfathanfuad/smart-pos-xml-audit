<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\AuditSnapshot;

class OrderRepository
{
    public function createOrder(array $data): Order
    {
        return Order::create($data);
    }

    public function createAuditSnapshot(int $orderId, string $xml, string $hash): AuditSnapshot
    {
        return AuditSnapshot::create([
            'order_id' => $orderId,
            'xml_payload' => $xml,
            'hash_signature' => $hash
        ]);
    }
}