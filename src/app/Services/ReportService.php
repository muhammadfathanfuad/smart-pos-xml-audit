<?php

namespace App\Services;

use App\Models\Order;
use App\Services\XMLAuditService;

class ReportService
{
    public function __construct(protected XMLAuditService $xmlService) {}

    public function getSalesSummary()
    {
        // Mengambil data order beserta snapshot XML-nya
        $orders = Order::with('auditSnapshot')->get();
        
        return $orders->map(function ($order) {
            $isValid = false;
            $xmlData = null;

            if ($order->auditSnapshot) {
                // 1. Verifikasi Hash XML untuk integritas data
                $isValid = $this->xmlService->verifyIntegrity(
                    $order->auditSnapshot->xml_payload,
                    $order->auditSnapshot->hash_signature
                );

                // 2. Parsing data XML untuk perbandingan (Cross-Check)
                if ($isValid) {
                    $xmlData = $this->xmlService->parseAuditData($order->auditSnapshot->xml_payload);
                    
                    // Business Logic: Pastikan total di DB sama dengan total di XML
                    if ($xmlData['total'] != $order->total_amount) {
                        $isValid = false; 
                    }
                }
            }

            return [
                'id_order'         => $order->id, // ID asli dari database untuk routing
                'invoice'          => $order->invoice_number,
                'db_total'         => $order->total_amount,
                'xml_total'        => $xmlData ? $xmlData['total'] : null,
                'integrity_status' => $isValid ? 'SECURE' : 'COMPROMISED',
                'date'             => $order->created_at->format('Y-m-d H:i:s')
            ];
        });
    }
}