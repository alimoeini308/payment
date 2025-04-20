<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('my_response')) {
    function my_response($data = null, $message = "success",$status=200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ],$status);
    }
}

if (!function_exists('max_amount')) {
    function max_amount(int $amount): int
    {
        $maxAmount = config('gateways.max_amount');
        return $amount <= $maxAmount ? $amount : $maxAmount;
    }
}
