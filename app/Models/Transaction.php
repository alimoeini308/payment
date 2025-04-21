<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = ['payment_id', 'amount', 'gateway', 'token', 'link', 'status', 'tracking_code', 'detail'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
