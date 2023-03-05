<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DesignationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/designation",
     *     summary="Get all designation",
     *     tags={"designation"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all designation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Associate Professor"),
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
        if(Designation::count() == 0)
        {
            return response()->json([], 204);
        }
        $designation = Designation::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $designation
        ], 200);
    }

    /**
     * @OA\Post(
     *      path="/api/designation",
     *      operationId="createdesignation",
     *      tags={"designation"},
     *      summary="Create new designation",
     *      description="Create new designation along with sessions and return created data",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="Professor"),
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
     *                     @OA\Property(property="name", type="string", example="professor"),
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
     *              @OA\Property(property="error", type="array",@OA\Items())
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
            'name' => 'required|max:50|min:5|unique:designation|string',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $designation = Designation::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'data' => $designation
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
     *      path="/api/designation/{id}",
     *      summary="Update a designation",
     *      description="Update a designation by id",
     *      operationId="updatedesignation",
     *      tags={"designation"},
     *      @OA\Parameter(
     *          name="id",
     *          description="designation ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="designation object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","location"},
     *              @OA\Property(property="name", type="string", example="main-branch"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="designation updated successfully",
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
     *                     @OA\Property(property="name", type="string", example="junior teacher"),
     *             )
     *              )
     *          )
     *      ),
     * )
     */

    public function update(Request $request, $id)
    {
        $designation = Designation::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:50|min:5|string',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $designation->update([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'data' => $designation
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
     *      path="/api/designation/{id}",
     *      summary="Delete a designation",
     *      operationId="designationDelete",
     *      security={{"bearerAuth":{}}},
     *      tags={"designation"},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the designation to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="designation deleted",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="status", type="boolean", example=true),
     *              ),
     *          ),
     *      ),
     * )
     */


    public function delete($id)
    {
        $designation = Designation::findOrFail($id);
        try
        {
            $designation->delete();

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
