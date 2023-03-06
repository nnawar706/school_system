<?php

namespace App\Http\Controllers;

use App\Models\Transportation;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TransportationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/transport",
     *     summary="Get all transport",
     *     tags={"transport"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all transport",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="transport_route_id", type="integer", example=1),
     *                     @OA\Property(property="driver_id", type="integer", example=1),
     *                     @OA\Property(property="vehicle_reg_no", type="string", example="Dhaka Metro-G-12-1112"),
     *                     @OA\Property(property="pickup_time", type="time", example="07:00:00"),
     *                     @OA\Property(property="transport_route", type="object",
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="name", type="string", example="rampura > hatirjheel > bangla motor"),
     *                     ),
     *                     @OA\Property(property="driver", type="object",
     *                          @OA\Property(property="id", type="integer", example=1),
     *                          @OA\Property(property="name", type="string", example="Mr. Driver"),
     *                          @OA\Property(property="phone_no", type="string", example="+8801638321911"),
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
        if(Transportation::count() == 0)
        {
            return response()->json([], 204);
        }
        $transport = Transportation::with(['transport_route' => function($query)
        {
            return $query->select('id','name');
        }])->with(['driver' => function($query) {
            return $query->select('id','name', 'phone_no');
        }])->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $transport
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/transport",
     *      operationId="createtransport",
     *      tags={"transport"},
     *      summary="Create new transport",
     *      description="Create new transport",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"transport_route_id", "driver_id", "vehicle_reg_no", "pickup_time"},
     *              @OA\Property(property="transport_route_id", type="integer", example=1),
     *              @OA\Property(property="driver_id", type="integer", example=1),
     *              @OA\Property(property="vehicle_reg_no", type="string", example="Dhaka Metro-G-12-1212"),
     *              @OA\Property(property="pickup_time", type="time", example="06:40:00"),
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
     *                     @OA\Property(property="transport_route_id", type="integer", example=1),
     *                     @OA\Property(property="driver_id", type="integer", example=1),
     *                     @OA\Property(property="vehicle_reg_no", type="string", example="Dhaka Metro-G-12-1212"),
     *                     @OA\Property(property="pickup_time", type="time", example="06:40:00"),
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
            'transport_route_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'vehicle_reg_no' => ['required', 'string', 'regex:/^[a-zA-Z\d\s]+(?:-[a-zA-Z\d]+){3}$/'],
            'pickup_time' => 'required|date_format:H:i',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $transport = Transportation::create([
                'transport_route_id' => $request->transport_route_id,
                'driver_id' => $request->driver_id,
                'vehicle_reg_no' => $request->vehicle_reg_no,
                'pickup_time' => $request->pickup_time
            ]);

            return response()->json([
                'status' => true,
                'data' => $transport
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
     *      path="/api/transport/{id}",
     *      summary="Update a transport",
     *      description="Update a transport by id",
     *      operationId="updatetransport",
     *      tags={"transport"},
     *      @OA\Parameter(
     *          name="id",
     *          description="transport ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="transport object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"transport_route_id","driver_id","vehicle_reg_no","pickup_time"},
     *              @OA\Property(property="transport_route_id", type="integer", example=1),
     *              @OA\Property(property="driver_id", type="integer", example=1),
     *              @OA\Property(property="vehicle_reg_no", type="string", example="Dhaka Metro-G-12-1212"),
     *              @OA\Property(property="pickup_time", type="time", example="06:40:00"),
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
     *                     @OA\Property(property="transport_route_id", type="integer", example=1),
     *                     @OA\Property(property="driver_id", type="integer", example=1),
     *                     @OA\Property(property="vehicle_reg_no", type="string", example="Dhaka Metro-G-12-1212"),
     *                     @OA\Property(property="pickup_time", type="time", example="06:40:00"),
     *             )
     *              )
     *          )
     *      ),
     * )
     */

    public function update(Request $request, $id)
    {
        $transport = Transportation::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'transport_route_id' => 'required|integer',
            'driver_id' => 'required|integer',
            'vehicle_reg_no' => ['required', 'string', 'regex:/^[a-zA-Z\d\s]+(?:-[a-zA-Z\d]+){3}$/'],
            'pickup_time' => 'required|date_format:H:i',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $transport->update([
                'transport_route_id' => $request->transport_route_id,
                'driver_id' => $request->driver_id,
                'vehicle_reg_no' => $request->vehicle_reg_no,
                'pickup_time' => $request->pickup_time
            ]);

            return response()->json([
                'status' => true,
                'data' => $transport
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
     *      path="/api/transport/{id}",
     *      operationId="deleteTransport",
     *      tags={"transport"},
     *      summary="Delete a transport",
     *      description="Delete a transport by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the transport",
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
        $transport = Transportation::findOrFail($id);
        try
        {
            $transport->delete();

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
