<?php

namespace App\Http\Controllers;

use App\Models\Context;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Contexts",
 *     description="Operations related to contexts"
 * )
 */

class ContextController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/contexts",
     *     tags={"Contexts"},
     *     summary="Get all contexts",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index()
    {
        return Context::all();
    }

    /**
     * @OA\Post(
     *     path="/api/contexts",
     *     tags={"Contexts"},
     *     summary="Create a new context",
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="default")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Context created",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="default")
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
            'name' => 'required|string|unique:contexts',
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], 422)
            );
        }

        $context = Context::create($validator->validated());

        return response()->json($context, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/contexts/{id}",
     *     tags={"Contexts"},
     *     summary="Get a single context",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Context found"),
     *     @OA\Response(response=404, description="Context not found")
     * )
     */
    public function show(Context $context)
    {
        return response()->json($context);
    }

    /**
     * @OA\Put(
     *     path="/api/contexts/{id}",
     *     tags={"Contexts"},
     *     summary="Update a context",
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
     *             @OA\Property(property="name", type="string", example="updated-context")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Context updated",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="updated-context")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="name", type="array",
     *                     @OA\Items(type="string", example="The name has already been taken.")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(Request $request, Context $context)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:contexts,name,' . $context->id,
        ]);

        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json(['errors' => $validator->errors()], 422)
            );
        }

        $context->update($validator->validated());

        return response()->json($context);
    }

    /**
     * @OA\Delete(
     *     path="/api/contexts/{id}",
     *     tags={"Contexts"},
     *     summary="Delete a context",
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Context deleted"),
     *     @OA\Response(response=404, description="Context not found")
     * )
     */
    public function destroy(Context $context)
    {
        $context->delete();

        return response()->json(null, 204);
    }
}
