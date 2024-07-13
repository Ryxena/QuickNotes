<?php

namespace App\Http\Controllers;

use App\Helper\ApiResponse;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('');
        return ApiResponse::success($categories, "Categories retrieved successfully");
    }
}
