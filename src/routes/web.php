<?php
use App\Models\Product;
use App\Services\ReportService;
use Illuminate\Support\Facades\Route;

Route::get('/pos', function () {
    return view('pos.index', ['products' => Product::all()]);
});

Route::get('/report', function (ReportService $reportService) {
    return view('report.index', ['reports' => $reportService->getSalesSummary()]);
});
