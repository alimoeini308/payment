<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['username','phone','description','amount'];
    protected $appends = ['payment_link'];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getPaymentLinkAttribute()
    {
        return route('payments.show.v1',['payment' => $this->getAttributeValue('id')]);
    }
}
