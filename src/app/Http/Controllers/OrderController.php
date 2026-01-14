<?php

namespace App\Http\Controllers;

use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ReportService;
use App\Services\XMLAuditService;

class OrderController extends Controller
{
    public function __construct(
        protected CheckoutService $checkoutService,
        protected ReportService $reportService,
        protected XMLAuditService $xmlService
    ) {}

    public function index(): JsonResponse
    {
        $reports = $this->reportService->getSalesSummary();
        return response()->json([
            'status' => 'success',
            'data' => $reports
        ]);
    }

    /**
     * Proses transaksi penjualan
     */
    public function checkout(Request $request): JsonResponse
    {
        // 1. Validasi Request
        $request->validate([
            'items' => 'required|array',
            'items.*.sku' => 'required|exists:products,sku',
            'items.*.qty' => 'required|integer|min:1',
            'payment_method' => 'required|in:cash,qris,transfer'
        ]);

        try {
            // 2. Panggil Service (Business Logic & XML Generation terjadi di sini)
            $order = $this->checkoutService->processCheckout(
                $request->items,
                $request->payment_method
            );

            // 3. Respon Sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil diproses',
                'data' => [
                    'invoice' => $order->invoice_number,
                    'total' => $order->total_amount,
                    'xml_snapshot_id' => $order->auditSnapshot->id // Menunjukkan bukti XML tersimpan
                ]
            ], 201);
        } catch (\Exception $e) {
            // 4. Respon Gagal (jika stok kurang atau error sistem)
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function exportXML()
    {
        $reports = $this->reportService->getSalesSummary();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sales_report/>');
        foreach ($reports as $data) {
            $node = $xml->addChild('transaction');
            $node->addChild('invoice', $data['invoice']);
            $node->addChild('amount', $data['db_total']);
            $node->addChild('status', $data['integrity_status']);
            $node->addChild('timestamp', $data['date']);
        }

        return response($xml->asXML(), 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="sales_report.xml"');
    }

    // Tambahkan di src/app/Http/Controllers/OrderController.php

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml'
        ]);

        try {
            $content = file_get_contents($request->file('xml_file')->getRealPath());

            // Parse & Re-hash data dari file
            $fileData = $this->xmlService->verifyUploadedXML($content);

            // Cari snapshot asli di database berdasarkan invoice
            $snapshot = \App\Models\AuditSnapshot::whereHas('order', function ($q) use ($fileData) {
                $q->where('invoice_number', $fileData['invoice']);
            })->first();

            if (!$snapshot) {
                return response()->json(['status' => 'invalid', 'message' => 'Invoice tidak ditemukan di sistem kami.'], 404);
            }

            // Bandingkan Hash Sistem vs Hash File yang diunggah
            if ($snapshot->hash_signature === $fileData['calculated_hash']) {
                return response()->json([
                    'status' => 'secure',
                    'message' => 'VERIFIKASI BERHASIL: File ini 100% asli dan belum dimodifikasi.',
                    'data' => $fileData
                ]);
            } else {
                return response()->json([
                    'status' => 'tampered',
                    'message' => 'PERINGATAN: File XML ini telah dimodifikasi secara ilegal!',
                    'data' => $fileData
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function downloadSnapshot($id)
    {
        $snapshot = \App\Models\AuditSnapshot::where('order_id', $id)->firstOrFail();

        $filename = "Certificate_INV_" . $snapshot->order_id . ".xml";

        return response($snapshot->xml_payload, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
