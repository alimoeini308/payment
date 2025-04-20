<?php

namespace App\Http\Controllers\admin\v1;

use App\Http\Controllers\Controller;
use App\Http\Services\Gateways\Contracts\Gateway;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payments()
    {
        return my_response(Payment::query()->with('transactions')->withSum(['transactions as total_paid' => function ($query) {
            $query->where('status','success');
        }],'amount')->latest()->paginate());
    }
}
