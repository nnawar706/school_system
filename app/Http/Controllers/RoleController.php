<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/role",
     *     summary="Get all role",
     *     description="All role fetched",
     *     tags={"role"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property( property="name", type="string", example="Admin"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *             ))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="204",
     *         description="Branch not found",
     *     )
     * )
     */


    public function index()
    {
        if(Role::count() == 0)
        {
            return response()->json([], 204);
        }

        $role = Role::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $role], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/role",
     *      operationId="createRole",
     *      tags={"role"},
     *      summary="Create new role",
     *      description="Create new role and return created data",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Student"),
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
     *                     @OA\Property(property="name", type="string", example="Student"),
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
            'name' => 'required|max:50|min:5|unique:role|alpha_dash'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $role = Role::create([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'data' => $role
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
     *     path="/api/role/{id}",
     *     summary="Get a single role",
     *     description="Retrieve a single role by ID",
     *     tags={"role"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the role to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property( property="name", type="string", example="Admin"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="204",
     *         description="Branch not found",
     *     )
     * )
     */


    public function read($id)
    {
        if($role = Role::find($id))
        {
            return response()->json([
                'status' => true,
                'data' => $role
            ], 200);
        }
        return response()->json([], 204);
    }


    /**
     * @OA\Put(
     *      path="/api/role/{id}",
     *      summary="Update a role",
     *      description="Update a role by id",
     *      operationId="updateRole",
     *      tags={"role"},
     *      @OA\Parameter(
     *          name="id",
     *          description="Role ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="Role object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Students"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Role updated successfully",
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
     *                     @OA\Property(property="name", type="string", example="Students"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *             )
     *              )
     *          )
     *      ),
     * )
     */


    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:5|alpha_dash'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $role->update([
                'name' => $request->name
            ]);

            return response()->json([
                'status' => true,
                'data' => $role
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
     *      path="/api/role/{id}",
     *      summary="Delete a role",
     *      operationId="roleDelete",
     *      security={{"bearerAuth":{}}},
     *      tags={"role"},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the role to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="role deleted",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="status", type="boolean", example=true),
     *              ),
     *          ),
     *      ),
     * )
     */


    public function delete($id)
    {
        $role = Role::findOrFail($id);
        try
        {
            $role->delete();

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
        Role::where('id', $id)->withTrashed()->restore();

        return response()->json([
            'status' => true], 200);
    }

    public function restoreAll()
    {
        Role::onlyTrashed()->restore();

        return response()->json([
            'status' => true], 200);
    }

    public function forceDelete($id)
    {
        Role::where('id', $id)->withTrashed()->forceDelete();

        return response()->json([
            'status' => true], 200);
    }
}
