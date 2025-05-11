<?php

namespace App\Http\Controllers;

use App\Models\Translation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Translations",
 *     description="API Endpoints for managing translations"
 * )
 */
class TranslationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/translations",
     *     summary="Get list of translations",
     *     tags={"Translations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="List of translations"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function index()
    {
        return Translation::with(['locale', 'context'])->paginate(10);
    }

    /**
     * @OA\Post(
     *     path="/api/translations",
     *     summary="Create a new translation",
     *     tags={"Translations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key", "content", "locale_id", "context_id"},
     *             @OA\Property(property="key", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="locale_id", type="integer"),
     *             @OA\Property(property="context_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Translation created"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|unique:translations',
            'content' => 'required|string',
            'locale_id' => 'required|exists:locales,id',
            'context_id' => 'required|exists:contexts,id',
        ]);

        $translation = Translation::create($request->all());

        return response()->json($translation, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/translations/{id}",
     *     summary="Get a specific translation",
     *     tags={"Translations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Translation details"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function show(Translation $translation)
    {
        return response()->json($translation);
    }

    /**
     * @OA\Put(
     *     path="/api/translations/{id}",
     *     summary="Update an existing translation",
     *     tags={"Translations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"key", "content", "locale_id", "context_id"},
     *             @OA\Property(property="key", type="string"),
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="locale_id", type="integer"),
     *             @OA\Property(property="context_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Translation updated"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function update(Request $request, Translation $translation)
    {
        $request->validate([
            'key' => 'required|string|unique:translations,key,' . $translation->id,
            'content' => 'required|string',
            'locale_id' => 'required|exists:locales,id',
            'context_id' => 'required|exists:contexts,id',
        ]);

        $translation->update($request->all());

        return response()->json($translation);
    }

    /**
     * @OA\Delete(
     *     path="/api/translations/{id}",
     *     summary="Delete a translation",
     *     tags={"Translations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Translation ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Deleted"),
     *     @OA\Response(response=404, description="Not found"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();

        return response()->json(null, 204);
    }

    /**
     * @OA\Get(
     *     path="/api/translations-search",
     *     summary="Search translations by key, content, and context",
     *     tags={"Translations"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="key",
     *         in="query",
     *         required=false,
     *         description="Search by translation key",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="content",
     *         in="query",
     *         required=false,
     *         description="Search by translation content",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="context",
     *         in="query",
     *         required=false,
     *         description="Filter by context name or identifier",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Number of records per page",
     *         @OA\Schema(type="integer", default=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A paginated list of translations matching the search criteria.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="key", type="string"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="locale_id", type="integer"),
     *                     @OA\Property(property="context_name", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(property="first_page_url", type="string"),
     *             @OA\Property(property="last_page", type="integer"),
     *             @OA\Property(property="last_page_url", type="string"),
     *             @OA\Property(property="next_page_url", type="string"),
     *             @OA\Property(property="per_page", type="integer"),
     *             @OA\Property(property="prev_page_url", type="string"),
     *             @OA\Property(property="to", type="integer"),
     *             @OA\Property(property="total", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request. The parameters provided are invalid or missing."
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error. Something went wrong on the server."
     *     )
     * )
     */
    public function search(Request $request)
    {
        $query = \App\Models\Translation::query()->with(['locale', 'context']);

        if ($request->filled('key')) {
            $query->where('key', 'like', '%' . $request->key . '%');
        }

        if ($request->filled('content')) {
            $query->where('content', 'like', '%' . $request['content'] . '%');
        }

        if ($request->filled('context')) {
            $query->whereHas('context', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->context . '%');
            });
        }

        $perPage = $request->input('per_page', 10);
        return response()->json($query->paginate($perPage));

    }
}
