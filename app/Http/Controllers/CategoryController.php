<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
class CategoryController extends Controller
{
    public function getCategory(){
        $category = Category::where('cat_type', 'House')->latest()->get();
        $response=CategoryResource::collection($category);
        return response($response,200);
    }
    public function getJobCategory()
{
    $category = Category::where('cat_type', 'Job')->latest()->get();
    $response = CategoryResource::collection($category);
    return response($response, 200);
}

public function addCategory(CategoryRequest $request)
{
    if($request->cat_type == "Job"){
        $existingCategory = Category::where('cat_name', $request->cat_name)
        ->where('cat_type', 'Job')
        ->first();
    }else if($request->cat_type == "House"){
        $existingCategory = Category::where('cat_name', $request->cat_name)
                                ->where('cat_type', 'House')
                                ->first();
    }
    

    if ($existingCategory) {
        return response([
            'message' => 'A category already exists.',
            'category' => $existingCategory
        ], 400); 
    }

    $category = Category::create([
        'cat_name' => $request->cat_name,
        'cat_type' => $request->cat_type,
    ]);

    $response = [
        'message' => 'Category Created',
        'id' => $category->id,
        'category' => $category,
    ];

    return response($response, 200);
}

}
