<?php

namespace App\Services;

class XMLAuditService
{
    private function normalizeXML(string $xmlContent): string
    {
        // Hilangkan spasi di awal/akhir dan pastikan line ending konsisten
        return trim(str_replace(["\r\n", "\r"], "\n", $xmlContent));
    }

    public function generateTransactionXML(array $orderData, array $items): string
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><transaction/>');

        $info = $xml->addChild('order_info');
        $info->addChild('invoice', $orderData['invoice_number']);
        $info->addChild('total', $orderData['total_amount']);
        $info->addChild('date', now()->toDateTimeString());

        $itemsNode = $xml->addChild('items');
        foreach ($items as $item) {
            $node = $itemsNode->addChild('item');
            $node->addChild('name', htmlspecialchars($item['name']));
            $node->addChild('qty', $item['qty']);
            $node->addChild('price_at_sale', $item['price']);
        }

        return $this->normalizeXML($xml->asXML());
    }

    public function signXML(string $xmlContent): string
    {
        // Selalu normalisasi sebelum di-hash
        $normalized = $this->normalizeXML($xmlContent);
        return hash('sha256', $normalized . config('app.key'));
    }

    public function verifyUploadedXML(string $xmlContent): array
    {
        try {
            $normalized = $this->normalizeXML($xmlContent);
            $xml = simplexml_load_string($normalized);

            if (!$xml) throw new \Exception("Format XML tidak valid.");

            $invoice = '';
            $amount = 0;

            // Logika Pintar: Mencari posisi Invoice berdasarkan struktur file
            if (isset($xml->order_info->invoice)) {
                // Struktur: Original Snapshot
                $invoice = (string)$xml->order_info->invoice;
                $amount  = (float)$xml->order_info->total;
            } elseif (isset($xml->transaction->invoice)) {
                // Struktur: Full Report (Mengambil transaksi pertama sebagai contoh)
                $invoice = (string)$xml->transaction->invoice;
                $amount  = (float)$xml->transaction->amount;
            } elseif (isset($xml->invoice)) {
                // Struktur: Simple Transaction
                $invoice = (string)$xml->invoice;
                $amount  = (float)$xml->amount;
            }

            if (empty($invoice)) {
                throw new \Exception("Struktur XML dikenali, tapi tag <invoice> tidak ditemukan.");
            }

            return [
                'invoice' => $invoice,
                'amount'  => $amount,
                'calculated_hash' => $this->signXML($normalized)
            ];
        } catch (\Exception $e) {
            throw new \Exception("Gagal memproses file: " . $e->getMessage());
        }
    }

    public function verifyIntegrity(string $xmlContent, string $storedHash): bool
    {
        // Menghitung ulang hash dari XML yang ada di DB
        $currentHash = $this->signXML($xmlContent);

        // Memeriksa apakah hash cocok (berarti file XML tidak dimodifikasi)
        return hash_equals($storedHash, $currentHash);
    }

    public function parseAuditData(string $xmlContent): array
    {
        $xml = simplexml_load_string($xmlContent);
        return [
            'invoice' => (string) $xml->order_info->invoice,
            'total'   => (float) $xml->order_info->total,
            'date'    => (string) $xml->order_info->date,
        ];
    }
}
