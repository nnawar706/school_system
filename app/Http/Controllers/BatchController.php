<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\LibraryShelf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BatchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/class",
     *     summary="Get all class",
     *     tags={"class"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all class",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Astrophysics"),
     *                     @OA\Property(property="branch", type="object",
     *                              @OA\Property(property="id", type="integer", example=1),
     *                              @OA\Property(property="name", type="string", example="main branch"),
     *                     ),
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
        if(Batch::count() == 0)
        {
            return response()->json([], 204);
        }

        $class = Batch::with(['branch' => function($query)
        {
            return $query->select('id','name');
        }
        ])->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $class
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/class",
     *      operationId="createclass",
     *      tags={"class"},
     *      summary="Create new class",
     *      description="Create new class",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="prep one"),
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
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="prep one"),
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
            'name' => 'required|max:30|min:5|string',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $class = Batch::create([
                'branch_id' => 1,
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'data' => $class
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
     *      path="/api/class/{id}",
     *      summary="Update a class",
     *      description="Update a class by id",
     *      operationId="updateclass",
     *      tags={"class"},
     *      @OA\Parameter(
     *          name="id",
     *          description="class ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="class object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="prep-two"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="class updated successfully",
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
     *                     @OA\Property(property="branch_id", type="integer", example="1", description="Branch ID"),
     *                     @OA\Property(property="name", type="string", example="main-branch", description="prep two"),
     *             )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Branch not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(
     *                  property="status",
     *                  type="boolean",
     *                  example=false
     *              ),
     *              @OA\Property(
     *                  property="error",
     *                  type="array",
     *                  @OA\Items(
     *                      type="string",
     *                      example="The name field must be at least 5 characters."
     *                  )
     *              )
     *          )
     *      )
     * )
     */

    public function update(Request $request, $id)
    {
        $class = Batch::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:30|min:5|string',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $class->update([
                'branch_id' => 1,
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'data' => $class
            ], 200);
        }
        catch(QueryException $ex)
        {
            return response()->json([
                'status' => false], 304);
        }
    }
}
