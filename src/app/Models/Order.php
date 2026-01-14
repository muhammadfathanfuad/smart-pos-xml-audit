<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = ['invoice_number', 'total_amount', 'tax_amount', 'payment_method'];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function auditSnapshot(): HasOne
    {
        return $this->hasOne(AuditSnapshot::class);
    }
}