<?php

namespace App\Http\Controllers;

use App\Models\LibraryShelf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LibraryShelfController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/library_shelf",
     *     summary="Get all library shelf",
     *     tags={"library shelf"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all library shelf",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
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
        $branch_id = (new AuthController)->getBranch();

        if(LibraryShelf::where('branch_id', $branch_id)->doesntExist())
        {
            return response()->json([], 204);
        }

        $shelf = LibraryShelf::with(['branch' => function($query)
        {
            return $query->select('id','name');
        }
        ])->where('branch_id', $branch_id)->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $shelf
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/library_shelf",
     *      operationId="createlibraryshelf",
     *      tags={"library shelf"},
     *      summary="Create new library shelf",
     *      description="Create new library shelf",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="astronomy"),
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
     *                     @OA\Property(property="name", type="string", example="astronomy"),
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
        $branch_id = (new AuthController)->getBranch();

        $validate = Validator::make($request->all(), [
            'name' => ['required','max:30','min:5','string',
                Rule::unique('library_shelf')->where(function ($query) use ($branch_id, $request) {
                    return $query->where('branch_id', $branch_id);
                })
            ],
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $shelf = LibraryShelf::create([
                'branch_id' => $branch_id,
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'data' => $shelf
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
     *      path="/api/library_shelf/{id}",
     *      summary="Update a library shelf",
     *      description="Update a library shelf by id",
     *      operationId="updatelibraryshelf",
     *      tags={"library shelf"},
     *      @OA\Parameter(
     *          name="id",
     *          description="library shelf ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="library shelf object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Literature"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Library shelf updated successfully",
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
     *                     @OA\Property(property="branch_id", type="integer", example="1"),
     *                     @OA\Property(property="name", type="string", example="Literature"),
     *             )
     *              )
     *          )
     *      )
     * )
     */


    public function update(Request $request, $id)
    {
        $shelf = LibraryShelf::findOrFail($id);

        $branch_id = (new AuthController)->getBranch();

        $validate = Validator::make($request->all(), [
            'name' => ['required','max:30','min:5','string',
                Rule::unique('library_shelf')->where(function ($query) use ($branch_id, $request) {
                    return $query->where('branch_id', $branch_id);
                })->ignore($id)
            ],
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $shelf->update([
                'branch_id' => $branch_id,
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'data' => $shelf
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
     *      path="/api/library_shelf/{id}",
     *      operationId="deletelibraryshelf",
     *      tags={"library shelf"},
     *      summary="Delete a library shelf",
     *      description="Delete a library shelf by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the library shelf",
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
        $shelf = LibraryShelf::findOrFail($id);
        try
        {
            $shelf->delete();

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
