<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('my_response')) {
    function my_response($data = null, $message = null,$status=200): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'data' => $data,
        ],$status);
    }
}
