<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\LibraryBookCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LibraryBookCategoryController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/library_book_category",
     *     summary="Get all library book category",
     *     tags={"library book category"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all categories",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="new cat"),
     *                 ),
     *             ),
     *         ),
     *     ),
     *
     *     @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false)
     *          )
     *      ),
     * )
     */


    public function index()
    {
        if(LibraryBookCategory::count() == 0)
        {
            return response()->json([], 204);
        }
        $cat = LibraryBookCategory::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $cat
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/library_book_category",
     *      operationId="createlibrary_book_category",
     *      tags={"library book category"},
     *      summary="Create new library_book_category",
     *      description="Create new library_book_category and return created data",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="new cat"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="201",
     *          description="Successful operation",
     *              @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="Branch ID"),
     *                     @OA\Property(property="name", type="string", example="new cat"),
     *               ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="Validation error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(property="error", type="array", @OA\Items(type="string"))
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="401",
     *          description="Unauthorized",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false)
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="500",
     *          description="Internal server error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false)
     *          )
     *      )
     * )
     */


    public function create(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:5|unique:library_book_category|string',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $cat = LibraryBookCategory::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'data' => $cat
            ], 201);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => false], 500);
        }
    }


    /**
     * @OA\Put(
     *      path="/api/library_book_category/{id}",
     *      summary="Update a library book category",
     *      description="Update a library_book_category by id",
     *      operationId="updatelibrary_book_category",
     *      tags={"library book category"},
     *      @OA\Parameter(
     *          name="id",
     *          description="library_book_category ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="library_book_category object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="something"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="library_book_category updated successfully",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=true
     *              ),
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                     @OA\Property(property="id", type="integer", example="1"),
     *                     @OA\Property(property="name", type="string", example="something"),
     *             )
     *              )
     *          )
     *      ),
     * )
     */


    public function update(Request $request, $id)
    {
        $cat = LibraryBookCategory::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:5|string|unique:library_book_category,name,'.$id,
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $cat->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'data' => $cat
            ], 200);
        }
        catch(QueryException $ex)
        {
            return response()->json([
                'status' => false], 304);
        }
    }


    /**
     * @OA\Delete(
     *      path="/api/library_book_category/{id}",
     *      operationId="deletelibrary_book_category",
     *      tags={"library book category"},
     *      summary="Delete a library_book_category",
     *      description="Delete a category by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the category",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=true),
     *          ),
     *      ),
     * )
     */


    public function delete($id)
    {
        $cat = LibraryBookCategory::findOrFail($id);
        try
        {
            $cat->delete();

            return response()->json([
                'status' => true], 200);
        }
        catch(QueryException $ex)
        {
            return response()->json([
                'status' => false
            ], 304);
        }
    }

}
