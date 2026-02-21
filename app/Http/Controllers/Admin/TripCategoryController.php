<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TripCategory;
use Illuminate\Http\Request;

class TripCategoryController extends Controller
{
    public function index()
    {
        $categories = TripCategory::all();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        TripCategory::create($data);

        return response()->json([
            'success' => true,
            'message' => __('Category created successfully'),
        ]);
    }

    public function show(TripCategory $category)
    {
        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }

    public function update(Request $request, TripCategory $category)
    {
        $data = $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Category updated successfully'),
        ]);
    }

    public function destroy(TripCategory $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => __('Category deleted successfully'),
        ]);
    }

    public function getData(Request $request)
    {
        $categories = TripCategory::latest()->get();

        return response()->json([
            'data' => $categories->map(function($category, $index) {
                return [
                    'id' => $index + 1,
                    'name_ar' => $category->name_ar,
                    'name_en' => $category->name_en,
                    'actions' => '
                        <div class="d-flex">
                            <button onclick="editCategory('.$category->id.')" class="btn btn-primary shadow btn-xs sharp me-1"><i class="fas fa-pencil-alt"></i></button>
                            <button onclick="deleteCategory('.$category->id.')" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></button>
                        </div>'
                ];
            })
        ]);
    }

    public function getAll()
    {
        return response()->json(TripCategory::all());
    }
}
