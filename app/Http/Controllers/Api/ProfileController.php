<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ProfileUpdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    // =============================
    // Profile
    // =============================

    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => __('auth.profile_retrieved'),
            'data'    => new UserResource($request->user()),
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $request->user()->update($request->only([
            'name', 'email', 'address', 'phone',
        ]));

        return response()->json([
            'success' => true,
            'message' => __('auth.profile_updated'),
            'data'    => new UserResource($request->user()->fresh()),
        ]);
    }
}