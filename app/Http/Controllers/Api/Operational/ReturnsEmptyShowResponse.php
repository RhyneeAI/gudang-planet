<?php

namespace App\Http\Controllers\Api\Operational;

trait ReturnsEmptyShowResponse
{
    protected function emptyShowResponse(string $message): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [],
        ]);
    }
}
