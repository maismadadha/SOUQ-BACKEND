<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        // 1️⃣ تأكد إن الملف موجود
        if (!$request->hasFile('image')) {
            return response()->json([
                'message' => 'No image sent'
            ], 400);
        }

        // 2️⃣ تحقق من المدخلات
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'product_id' => 'required|integer|exists:products,id',
        ]);

        $file = $request->file('image');
        $productId = $request->input('product_id');

        // 3️⃣ تخزين الصورة (داخل storage/app/public/uploads)
        $path = $file->store('uploads', 'public');
        // مثال: uploads/abc123.jpg

        // 4️⃣ تخزين PATH فقط في الداتابيس (وهذا المهم 🔥)
        $image = ProductImage::create([
            'product_id' => $productId,
            'image_url'  => $path,
        ]);

        // 5️⃣ نرجع URL كامل للتطبيق
        $fullUrl = asset('storage/' . $path);

        return response()->json([
            'message' => 'Image uploaded successfully',
            'url'     => $fullUrl
        ], 201);
    }
}
