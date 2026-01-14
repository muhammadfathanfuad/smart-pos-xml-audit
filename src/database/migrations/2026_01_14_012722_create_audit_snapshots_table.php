<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
    Schema::create('audit_snapshots', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->unique()->constrained()->onDelete('cascade');
        $table->longText('xml_payload'); // Tempat menyimpan data XML transaksi
        $table->string('hash_signature'); // Hash untuk validasi integritas XML
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_snapshots');
    }
};
