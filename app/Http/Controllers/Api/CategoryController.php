<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::with('createdBy') 
            ->when($request->search, function ($query, $search) {
                // Case-insensitive search using LOWER() for PostgreSQL and MySQL compatibility
                $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->orderBy('name')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => __('categories.list'),
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'created_by' => $request->user()->id,
            'company_id' => $request->user()->company_id,
        ]);

        $category->load('createdBy');

        return response()->json([
            'success' => true,
            'message' => __('categories.stored'),
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function show(Category $category)
    {
        $category->loadMissing('createdBy');

        return response()->json([
            'success' => true,
            'message' => __('categories.detail'),
            'data' => new CategoryResource($category),
        ]);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        if ($request->has('name')) {
            $category->update(['name' => $request->name]);
        }

        $category->load('createdBy');

        return response()->json([
            'success' => true,
            'message' => __('categories.updated'),
            'data' => new CategoryResource($category),
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => __('categories.has_products'),
                'code' => 422,
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => __('categories.deleted'),
        ]);
    }
}