<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/driver",
     *     summary="Get all driver",
     *     tags={"driver"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all drivers",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Mr Driver"),
     *                     @OA\Property(property="license_no", type="string", example="12211221"),
     *                     @OA\Property(property="phone_no", type="string", example="12211221"),
     *                     @OA\Property(property="nid_no", type="string", example="12211221"),
     *                     @OA\Property(property="photo_url", type="string", example="http://192.168.68.128:8002/storage/images/driver/driver_1677746155"),
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
        if($driver = Driver::latest()->get())
        {
            return response()->json([
                'status' => true,
                'data' => $driver], 200);
        }

        return response()->json([], 204);
    }


    /**
     * @OA\Get(
     *     path="/api/driver/{id}",
     *     summary="Get a single driver",
     *     description="Retrieve a single driver by ID",
     *     tags={"driver"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the driver to retrieve",
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
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Mr Driver"),
     *                     @OA\Property(property="license_no", type="string", example="12211221"),
     *                     @OA\Property(property="phone_no", type="string", example="12211221"),
     *                     @OA\Property(property="nid_no", type="string", example="12211221"),
     *                     @OA\Property(property="photo_url", type="string", example="http://192.168.68.128:8002/storage/images/driver/driver_1677746155"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="204",
     *         description="Driver not found",
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
        if($driver = Driver::find($id))
        {
            return response()->json([
                'status' => true,
                'data' => $driver
            ]);
        }
        return response()->json([], 204);
    }


    /**
     * @OA\Post(
     *      path="/api/driver",
     *      operationId="createdriver",
     *      tags={"driver"},
     *      summary="Create new driver",
     *      description="Create new driver and return created data",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","license_no", "phone_no", "nid_no", "photo_url"},
     *              @OA\Property(property="name", type="string", example="Mr Driver"),
     *                     @OA\Property(property="license_no", type="string", example="12211221"),
     *                     @OA\Property(property="phone_no", type="string", example="12211221"),
     *                     @OA\Property(property="nid_no", type="string", example="12211221"),
     *                     @OA\Property(property="photo_url", type="file", example="image1.png"),
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
     *                     @OA\Property(property="name", type="string", example="Mr Driver"),
     *                     @OA\Property(property="license_no", type="string", example="12211221"),
     *                     @OA\Property(property="phone_no", type="string", example="12211221"),
     *                     @OA\Property(property="nid_no", type="string", example="12211221"),
     *                     @OA\Property(property="photo_url", type="string", example="http://192.168.68.128:8002/storage/images/driver/driver_1677746155"),
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
            'name' => 'required|max:255|min:5|string',
            'license_no' => 'required|unique:driver',
            'phone_no' => 'required|unique:driver',
            'nid_no' => 'required|unique:driver',
            'photo_url' => 'required|image|unique:driver|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $photo = $request->file('photo_url');
            $photo_file = "driver_" . time() . rand(10, 100);
            $photo_path = $photo->storeAs('public/images/driver', $photo_file);
            $photoURL = Storage::url($photo_path);

            $driver = Driver::create([
                'name' => $request->name,
                'license_no' => $request->license_no,
                'phone_no' => $request->phone_no,
                'nid_no' => $request->nid_no,
                'photo_url' => "http://192.168.68.128:8002" . $photoURL,
            ]);

            return response()->json([
                'status' => true,
                'data' => $driver
            ], 200);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => false], 500);
        }
    }


    /**
     * @OA\Post(
     *      path="/api/driver/{id}",
     *      summary="Update a driver",
     *      description="Update a driver by id",
     *      operationId="updatedriver",
     *      tags={"driver"},
     *      @OA\Parameter(
     *          name="id",
     *          description="driver ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="driver object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","license_no", "phone_no", "nid_no", "photo_url"},
     *              @OA\Property(property="name", type="string", example="Mr Driver"),
     *                     @OA\Property(property="license_no", type="string", example="12211221"),
     *                     @OA\Property(property="phone_no", type="string", example="12211221"),
     *                     @OA\Property(property="nid_no", type="string", example="12211221"),
     *                     @OA\Property(property="photo_url", type="file", example="image1.png"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Driver updated successfully",
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
     *                     @OA\Property(property="name", type="string", example="Mr Driver"),
     *                     @OA\Property(property="license_no", type="string", example="12211221"),
     *                     @OA\Property(property="phone_no", type="string", example="12211221"),
     *                     @OA\Property(property="nid_no", type="string", example="12211221"),
     *                     @OA\Property(property="photo_url", type="file", example="image1.png"),
     *             )
     *              )
     *          )
     *      ),
     * )
     */


    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'name' => 'required|max:255|min:5|string|unique:driver,name,'.$id,
            'license_no' => 'required',
            'phone_no' => 'required',
            'nid_no' => 'required',
//            'photo_url' => 'image|mimes:jpeg,png,jpg|max:2048|nullable',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $driver->name = $request->input('name');
            $driver->license_no = $request->input('license_no');
            $driver->phone_no = $request->input('phone_no');
            $driver->nid_no = $request->input('nid_no');

            if ($request->hasFile('photo_url'))
            {
                if($driver->photo_url)
                {
                    $prev_photo_url = $driver->photo_url;
                    $prev_photo = str_replace('http://192.168.68.128:8002/storage', 'public', $prev_photo_url);
                    Storage::delete($prev_photo);
                }
                $photo = $request->file('photo_url');
                $photo_file = "driver_" . time() . rand(10, 100);
                $photo_path = $photo->storeAs('public/images/driver', $photo_file);
                $photoURL = Storage::url($photo_path);

                $driver->photo_url = "http://192.168.68.128:8002" . $photoURL;
            }

            $driver->save();

            return response()->json([
                'status' => true,
                'data' => $driver
            ], 200);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => false], 304);
        }
    }

    /**
     * @OA\Delete(
     *      path="/api/driver/{id}",
     *      operationId="deletedriver",
     *      tags={"driver"},
     *      summary="Delete a driver",
     *      description="Delete a driver by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the driver",
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
        $driver = Driver::findOrFail($id);
        try
        {
            $driver->delete();

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
