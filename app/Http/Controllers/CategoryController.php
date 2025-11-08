<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // ✅ عرض كل الفئات
    public function index()
    {
        return response()->json(Category::all());
    }

    // ✅ عرض فئة محددة
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return response()->json($category);
    }

    // ✅ إنشاء فئة جديدة مع رفع صورة
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:categories',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = ['name' => $request->name];

        // 🔹 إذا تم رفع صورة
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $data['image'] = $path;
        }

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    // ✅ تعديل فئة (مع إمكانية تغيير الصورة)
    public function update(Request $request, $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|unique:categories,name,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->has('name')) {
            $category->name = $request->name;
        }

        // 🔹 إذا تم رفع صورة جديدة
        if ($request->hasFile('image')) {
            // احذف الصورة القديمة إذا كانت موجودة
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $path = $request->file('image')->store('categories', 'public');
            $category->image = $path;
        }

        $category->save();

        return response()->json($category);
    }

    // ✅ حذف فئة
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // احذف الصورة من التخزين إن وجدت
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
