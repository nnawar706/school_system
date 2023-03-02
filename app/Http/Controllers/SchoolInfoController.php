<?php

namespace App\Http\Controllers;

use App\Models\SchoolInfo;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SchoolInfoController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/school_info/{id}",
     *     summary="Get a single school_info",
     *     description="Retrieve a single school info by ID",
     *     tags={"school info"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the school info to retrieve",
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
     *                     @OA\Property(property="school_name", type="string", example="cosmo"),
     *                     @OA\Property(property="logo_url", type="file", example="image1.png"),
     *                     @OA\Property(property="favicon_url", type="file", example="image2.png"),
     *                     @OA\Property(property="email", type="email", example="cosmo@edu.com.bd"),
     *                     @OA\Property(property="phone_no", type="string", example="12345678"),
     *                     @OA\Property(property="facebook_url", type="string", example="https://google.com"),
     *                     @OA\Property(property="linkedin_url", type="string", example="https://google.com"),
     *                     @OA\Property(property="about", type="text", example="This is a school"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="204",
     *         description="School info not found",
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
        if($info = SchoolInfo::find($id))
        {
            return response()->json([
                'status' => true,
                'data' => $info
            ]);
        }
        return response()->json([], 204);
    }


    /**
     * @OA\Put(
     *      path="/api/school_info/{id}",
     *      summary="Update school info",
     *      description="Update school info by id",
     *      operationId="updateschool_info",
     *      tags={"school info"},
     *      @OA\Parameter(
     *          name="id",
     *          description="school_info ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="School info object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"school_name", "logo_url", "favicon_url", "email", "phone_no", "facebook_url", "linkedin_url", "about"},
     *              @OA\Property(property="school_name", type="string", example="cosmo"),
     *              @OA\Property(property="logo_url", type="file", example="image1.png"),
     *              @OA\Property(property="favicon_url", type="file", example="image2.png"),
     *              @OA\Property(property="email", type="email", example="cosmo@edu.com.bd"),
     *              @OA\Property(property="phone_no", type="string", example="12345678"),
     *              @OA\Property(property="facebook_url", type="string", example="https://google.com"),
     *              @OA\Property(property="linkedin_url", type="string", example="https://google.com"),
     *              @OA\Property(property="about", type="text", example="This is a school"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Schoole info updated successfully",
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
     *                     @OA\Property(property="school_name", type="string", example="cosmo"),
     *                     @OA\Property(property="logo_url", type="file", example="image1.png"),
     *                     @OA\Property(property="favicon_url", type="file", example="image2.png"),
     *                     @OA\Property(property="email", type="email", example="cosmo@edu.com.bd"),
     *                     @OA\Property(property="phone_no", type="string", example="12345678"),
     *                     @OA\Property(property="facebook_url", type="string", example="https://google.com"),
     *                     @OA\Property(property="linkedin_url", type="string", example="https://google.com"),
     *                     @OA\Property(property="about", type="text", example="This is a school"),
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
        $school_info = SchoolInfo::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'school_name' => 'required|max:255|min:5|string',
            'logo_url' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'favicon_url' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'email' => 'required|email|max:255',
            'phone_no' => 'required',
            'facebook_url' => 'required|url',
            'linkedin_url' => 'required|url',
            'about' => 'required|string'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $logo = $request->file('logo_url');
            $logo_file = "school_logo_" . time();
            $logo_path = $logo->storeAs('public/images/school', $logo_file);
            $logoURL = Storage::url($logo_path);

            $favicon = $request->file('favicon_url');
            $favicon_file = "school_favicon_" . time();
            $favicon_path = $favicon->storeAs('public/images/school', $favicon_file);
            $faviconURL = Storage::url($favicon_path);

            $school_info->update([
                'school_name' => $request->school_name,
                'logo_url' => "http://192.168.68.128:8002" . $logoURL,
                'favicon_url' => "http://192.168.68.128:8002" . $faviconURL,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'facebook_url' => $request->facebook_url,
                'linkedin_url' => $request->linkedin_url,
                'about' => $request->about
            ]);

            return response()->json([
                'status' => true,
                'data' => $school_info
            ], 200);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => false], 304);
        }
    }
}
