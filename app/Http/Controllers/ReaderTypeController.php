<?php

namespace App\Http\Controllers;

use App\Models\LibraryShelf;
use App\Models\ReaderType;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReaderTypeController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/reader_type",
     *     summary="Get all reader type",
     *     tags={"reader type"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all reader type",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Toddler"),
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
     *      @OA\Response(
     *          response="204",
     *          description="No data",
     *      ),
     * )
     */


    public function index()
    {
        if(ReaderType::count() == 0)
        {
            return response()->json([], 204);
        }
        $type = ReaderType::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $type
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/reader_type",
     *      operationId="createreader_type",
     *      tags={"reader type"},
     *      summary="Create new reader type",
     *      description="Create new reader type",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="toddler"),
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
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="toddler"),
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
            'name' => 'required|max:30|min:5|string|unique:reader_type',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $type = ReaderType::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'data' => $type
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
     *      path="/api/reader_type/{id}",
     *      summary="Update a reader type",
     *      description="Update a reader type by id",
     *      operationId="updatebookreader",
     *      tags={"reader type"},
     *      @OA\Parameter(
     *          name="id",
     *          description="reader type ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="reader type object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Early Reader"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="book reader updated successfully",
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
     *                     @OA\Property(property="name", type="string", example="Early Reader"),
     *             )
     *              )
     *          )
     *      )
     * )
     */


    public function update(Request $request, $id)
    {
        $type = ReaderType::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:30|min:5|string|unique:reader_type,name,'.$id,
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $type->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'data' => $type
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
     *      path="/api/reader_type/{id}",
     *      operationId="deletereadertype",
     *      tags={"reader type"},
     *      summary="Delete a reader type",
     *      description="Delete a reader type by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the reader type",
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
        $type = ReaderType::findOrFail($id);
        try
        {
            $type->delete();

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
