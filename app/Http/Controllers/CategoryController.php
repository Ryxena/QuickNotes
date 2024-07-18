<?php

namespace App\Http\Controllers;

use App\Helper\ApiResponse;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $user = auth()->user()->id;
        $categories = Category::where('is_public', true)
            ->orWhere('users_id', $user)
            ->orderBy('name')
            ->get();

        return ApiResponse::success($categories, 'Categories retrieved successfully');
    }

    public function create(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation failed', $validator->errors(), 422);
        }

        $category = new Category($request->only(['name']));

        $category->users_id = auth()->user()->id;

        $category->save();

        return ApiResponse::success($category, 'Category created successfully', 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $user = auth()->user();
        $category = Category::where('id', $id)->where('users_id', $user->id)->first();
        if (! $category) {
            return ApiResponse::error('Categories not found');
        }
        $category->name = $request->name;
        $category->save();

        return ApiResponse::success($category, 'Success update category name');
    }

    public function delete(Request $request, $id): JsonResponse
    {
        $user = auth()->user();
        $note = Category::where('id', $id)->where('users_id', $user->id)->first();
        if (! $note) {
            return ApiResponse::error('Note not found');
        }
        $note->delete();

        return ApiResponse::success(null, 'Note deleted successfully');

    }
}
