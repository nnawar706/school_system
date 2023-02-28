<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class BranchController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/branch",
     *     summary="Get all branches",
     *     tags={"branch"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all branches",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Main Branch"),
     *                     @OA\Property(property="location", type="string", example="Banasree"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
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
        if(Branch::count() == 0)
        {
            return response()->json([], 204);
        }

        $branch = Branch::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $branch], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/branch",
     *      operationId="createBranch",
     *      tags={"branch"},
     *      summary="Create new branch",
     *      description="Create new branch and return created data",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","location"},
     *              @OA\Property(property="name", type="string", example="main-branch"),
     *              @OA\Property(property="location", type="string", example="Dhaka"),
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
     *                     @OA\Property(property="name", type="string", example="main-branch", description="Branch name"),
     *                     @OA\Property(property="location", type="string", example="Dhaka", description="Branch location"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
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
            'name' => 'required|max:50|min:5|unique:branch|string',
            'location' => 'required|max:255|min:5'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $branch = Branch::create([
                'name' => $request->name,
                'location' => $request->location
            ]);

            return response()->json([
                'status' => true,
                'data' => $branch
            ], 201);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => false], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/branch/{id}",
     *     summary="Get a single branch",
     *     description="Retrieve a single branch by ID",
     *     tags={"branch"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the branch to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                     @OA\Property(property="id", type="integer", example="1", description="Branch ID"),
     *                     @OA\Property(property="name", type="string", example="main-branch", description="Branch name"),
     *                     @OA\Property(property="location", type="string", example="mohakhali DOHS", description="Branch location"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="204",
     *         description="Branch not found",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *         )
     *     )
     * )
     */

    public function read($id)
    {
        if($branch = Branch::find($id))
        {
            return response()->json([
                'status' => true,
                'data' => $branch
            ]);
        }
        return response()->json([], 204);
    }

    /**
     * @OA\Put(
     *      path="/api/branch/{id}",
     *      summary="Update a branch",
     *      description="Update a branch by id",
     *      operationId="updateBranch",
     *      tags={"branch"},
     *      @OA\Parameter(
     *          name="id",
     *          description="Branch ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="Branch object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","location"},
     *              @OA\Property(property="name", type="string", example="main-branch"),
     *              @OA\Property(property="location", type="string", example="mohakhali"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Branch updated successfully",
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
     *                     @OA\Property(property="id", type="integer", example="1", description="Branch ID"),
     *                     @OA\Property(property="name", type="string", example="main-branch", description="Branch name"),
     *                     @OA\Property(property="location", type="string", example="mohakhali DOHS", description="Branch location"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
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
        $branch = Branch::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:5|alpha_dash',
            'location' => 'required|max:255'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $branch->update([
                'name' => $request->name,
                'location' => $request->location
            ]);

            return response()->json([
                'status' => true,
                'data' => $branch
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
     *      path="/api/branch/{id}",
     *      operationId="deleteBranch",
     *      tags={"branch"},
     *      summary="Delete a branch",
     *      description="Delete a branch by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the branch",
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
     *     @OA\Response(
     *          response=304,
     *          description="Database error",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Branch not found",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false),
     *          ),
     *      ),
     * )
     */


    public function delete($id)
    {
        $branch = Branch::findOrFail($id);
        try
        {
            $branch->delete();

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

    public function restore($id)
    {
        Branch::where('id', $id)->withTrashed()->restore();

        return response()->json([
            'status' => true], 200);
    }

    public function restoreAll()
    {
        Branch::onlyTrashed()->restore();

        return response()->json([
            'status' => true], 200);
    }

    public function forceDelete($id)
    {
        Branch::where('id', $id)->withTrashed()->forceDelete();

        return response()->json([
            'status' => true], 200);
    }

}
