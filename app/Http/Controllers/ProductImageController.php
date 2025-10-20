<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductImage;

class ProductImageController extends Controller
{
    public function index()
    {
        $images = ProductImage::with('product')->get();
        return response()->json($images);
    }


    public function show($id)
    {
        $image = ProductImage::with('product')->find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }
        return response()->json($image);
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'image_url' => 'required|string',
        ]);

        $image = ProductImage::create($data);
        return response()->json($image, 201);
    }


    public function update(Request $request, $id)
    {
        $image = ProductImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $data = $request->validate([
            'image_url' => 'required|string',
        ]);

        $image->update($data);
        return response()->json($image);
    }

    
    public function destroy($id)
    {
        $image = ProductImage::find($id);
        if (!$image) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        $image->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }
}
