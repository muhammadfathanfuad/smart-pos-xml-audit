<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditSnapshot extends Model
{
    protected $fillable = ['order_id', 'xml_payload', 'hash_signature'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}