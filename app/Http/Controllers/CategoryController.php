<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // عرض جميع الفئات (GET)
    public function index()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories], 200);
    }

    // إنشاء فئة جديدة (POST)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json(['message' => 'تم إنشاء الفئة بنجاح', 'category' => $category], 201);
    }

    // عرض فئة واحدة (GET)
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'الفئة غير موجودة'], 404);
        }
        return response()->json(['category' => $category], 200);
    }

    // تحديث فئة (PUT/PATCH)
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'الفئة غير موجودة'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        $category->update([
            'name' => $request->name ?? $category->name,
            'description' => $request->description ?? $category->description,
        ]);

        return response()->json(['message' => 'تم تحديث الفئة بنجاح', 'category' => $category], 200);
    }

    // حذف فئة (DELETE)
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'الفئة غير موجودة'], 404);
        }

        $category->delete();
        return response()->json(['message' => 'تم حذف الفئة بنجاح'], 200);
    }
}