<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SliderAd;

class SliderAdController extends Controller
{
    public function index()
    {
        $ads = SliderAd::with('store')->get();
        return response()->json($ads);
    }

    public function show($id)
    {
        $ad = SliderAd::with('store')->find($id);
        if (!$ad) {
            return response()->json(['message' => 'SliderAd not found'], 404);
        }
        return response()->json($ad);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'store_id' => 'required|exists:users,id',
            'image_url' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $ad = SliderAd::create($data);
        return response()->json($ad, 201);
    }

    public function update(Request $request, $id)
    {
        $ad = SliderAd::find($id);
        if (!$ad) {
            return response()->json(['message' => 'SliderAd not found'], 404);
        }

        $data = $request->validate([
            'store_id' => 'sometimes|exists:users,id',
            'image_url' => 'sometimes|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
        ]);

        $ad->update($data);
        return response()->json($ad);
    }

    public function destroy($id)
    {
        $ad = SliderAd::find($id);
        if (!$ad) {
            return response()->json(['message' => 'SliderAd not found'], 404);
        }

        $ad->delete();
        return response()->json(['message' => 'SliderAd deleted successfully']);
    }
}
