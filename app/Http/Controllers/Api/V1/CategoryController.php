<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->input('per_page', 15);
        $categories = Category::where('company_id', $request->user()->company_id)
            ->orderBy('name')
            ->paginate($per_page);

        return response()->json([
            'success' => true,
            'message' => __('categories.list'),
            'data'    => CategoryResource::collection($categories),
        ], 200);
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name'       => $request->name,
            'company_id' => $request->user()->company_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('categories.stored'),
            'data'    => new CategoryResource($category),
        ], 201);
    }

    public function show(Request $request, Category $category)
    {
        if ($category->company_id !== $request->user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => __('categories.unauthorized'),
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => __('categories.detail'),
            'data'    => new CategoryResource($category), // Pakai Resource, bukan Collection
        ], 200);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        if ($category->company_id !== $request->user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => __('categories.unauthorized'),
            ], 403);
        }

        $category->update(['name' => $request->name]);

        return response()->json([
            'success' => true,
            'message' => __('categories.updated'),
            'data'    => new CategoryResource($category),
        ], 200);
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->company_id !== $request->user()->company_id) {
            return response()->json([
                'success' => false,
                'message' => __('categories.unauthorized'),
            ], 403);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => __('categories.deleted'),
        ], 200); 
    }
}