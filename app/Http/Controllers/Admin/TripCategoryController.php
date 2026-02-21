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

    public function getAll()
    {
        return response()->json(TripCategory::all());
    }
}
