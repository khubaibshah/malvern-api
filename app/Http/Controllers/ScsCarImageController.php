<?php

// app/Http/Controllers/ScsCarImageController.php
namespace App\Http\Controllers;

use App\Models\ScsCarImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScsCarImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'car_images' => 'required|array|max:30',
            'car_images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // 2MB max per image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $images = $request->file('car_images');

        foreach ($images as $image) {
            $scsCarImage = new ScsCarImage();
            $scsCarImage->car_image = file_get_contents($image);
            $scsCarImage->save();
        }

        return response()->json(['message' => 'Images successfully uploaded'], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $scsCarImage = ScsCarImage::find($id);

        if (!$scsCarImage) {
            return response()->json(['message' => 'Image not found'], 404);
        }

        return response()->json(['image' => $scsCarImage->car_image]);
    }
}

