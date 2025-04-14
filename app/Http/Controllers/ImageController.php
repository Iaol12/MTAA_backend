<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{

/**
 * @OA\Post(
 *     path="/api/upload",
 *     tags={"Image"},
 *     summary="Uploads an image to the backend server",
 *     description="This endpoint allows users to upload an image file to the server. The image must not exceed 2MB in size.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"image"},
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="The image file to upload"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Image uploaded successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="path",
 *                 type="string",
 *                 description="The path to the uploaded image"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 description="Error message"
 *             )
 *         )
 *     )
 * )
 */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048', // max 2MB
        ]);

        $path = $request->file('image')->store('images', 'public');

        return response()->json(['path' => $path], 201);
    }
}
