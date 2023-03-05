<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\TransportRoute;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransportRouteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/transport_route",
     *     summary="Get all transport route",
     *     tags={"transport route"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all transport route",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Main Branch"),
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
        if(TransportRoute::count() == 0)
        {
            return response()->json([], 204);
        }
        $transport_route = TransportRoute::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $transport_route
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/transport_route",
     *      operationId="createtransportroute",
     *      tags={"transport route"},
     *      summary="Create new transport route",
     *      description="Create new transport route",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="rampura > hatirjheel > bangla motor"),
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
     *                     @OA\Property(property="id", type="integer", example=1,),
     *                     @OA\Property(property="name", type="string", example="rampura > hatirjheel > bangla motor"),
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
            'name' => ['required', 'unique', 'max:50', 'min:5', 'regex:/^[a-zA-Z\s]+(?:>[a-zA-Z\s\d]+)*$/'],
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $route = TransportRoute::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'status' => true,
                'data' => $route
            ], 201);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => $ex->getMessage()], 500);
        }
    }


    /**
     * @OA\Put(
     *      path="/api/transport_route/{id}",
     *      summary="Update a transport route",
     *      description="Update a transport route by id",
     *      operationId="updatetransportroute",
     *      tags={"transport route"},
     *      @OA\Parameter(
     *          name="id",
     *          description="transport route ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="transport route object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","location"},
     *              @OA\Property(property="name", type="string", example="rampura > hatirjheel > bangla motor"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="transport route updated successfully",
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
     *                     @OA\Property(property="name", type="string", example="rampura > hatirjheel > bangla motor"),
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
            'name' => ['required','max:50','min:5','string', 'regex:/^[a-zA-Z]+(?:\s>[a-zA-Z]+)*$/'],
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
     *      path="/api/transport_route/{id}",
     *      summary="Permanently delete a transport route",
     *      operationId="routeDelete",
     *      security={{"bearerAuth":{}}},
     *      tags={"transport route"},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the transport route to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="transport route deleted",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="status", type="boolean", example=true),
     *              ),
     *          ),
     *      ),
     * )
     */


    public function delete($id)
    {
        $route = TransportRoute::findOrFail($id);
        try
        {
            TransportRoute::where('id', $id)->withTrashed()->forceDelete();

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
