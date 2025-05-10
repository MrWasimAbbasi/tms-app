<?php

namespace App\Http\Controllers;

use App\Models\Locale;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 *
 * @OA\Tag(
 *     name="Locales",
 *     description="Operations related to locales"
 * )
 *
 */
class LocaleController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/locales",
     *     tags={"Locales"},
     *     summary="Get all locales",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return Locale::all();
    }

    /**
     * @OA\Post(
     *     path="/api/locales",
     *     tags={"Locales"},
     *     summary="Create a new locale",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="en"),
     *             @OA\Property(property="description", type="string", example="English", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Locale created",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="en"),
     *             @OA\Property(property="description", type="string", example="English")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:locales',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], 422)
            );
        }

        $locale = Locale::create($validator->validated());

        return response()->json($locale, 201);
    }


    /**
     * @OA\Get(
     *     path="/api/locales/{id}",
     *     tags={"Locales"},
     *     summary="Get a single locale",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Locale found"),
     *     @OA\Response(response=404, description="Locale not found")
     * )
     */
    public function show(Locale $locale)
    {
        return response()->json($locale);
    }

    /**
     * @OA\Put(
     *     path="/api/locales/{id}",
     *     tags={"Locales"},
     *     summary="Update a locale",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="en"),
     *             @OA\Property(property="description", type="string", example="English", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Locale updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="en"),
     *             @OA\Property(property="description", type="string", example="English")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Locale not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="string", example="Locale not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, Locale $locale)
    {
        if (!$locale) {
            return response()->json(['errors' => 'Locale not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:locales,name,' . $locale->id,
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], 422)
            );
        }

        $locale->update($validator->validated());

        return response()->json($locale);
    }

    /**
     * @OA\Delete(
     *     path="/api/locales/{id}",
     *     tags={"Locales"},
     *     summary="Delete a locale",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Locale deleted"),
     *     @OA\Response(response=404, description="Locale not found")
     * )
     */
    public function destroy(Locale $locale)
    {
        $locale->delete();

        return response()->json(null, 204);
    }
}
