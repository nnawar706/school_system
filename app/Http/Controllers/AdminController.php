<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/admin",
     *      summary="Get all admin",
     *      security={{"bearerAuth":{}}},
     *      tags={"admin"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="All admin fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="user_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="string", example="Mr. admin"),
     *                                  @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *                                  @OA\Property(property="phone_no", type="string", example="01756439189"),
     *                                  @OA\Property(property="nid_no", type="string", example="56439189"),
     *                                  @OA\Property(property="religion_id", type="integer", example=2),
     *                                  @OA\Property(property="dob", type="date", example="2022-02-02"),
     *                                  @OA\Property(property="gender_id", type="integer", example=2),
     *                                  @OA\Property(property="profile_photo_url", type="string", example="img1.png"),
     *                                  @OA\Property(property="status", type="integer", example=1),
     *                                  @OA\Property(property="user", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="registration_id", type="string", example="0000000"),
     *                                  ),
     *                                  @OA\Property(property="religion", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="Christian"),
     *                                      ),
     *                                  @OA\Property(property="designation", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="professor"),
     *                                  ),
     *                                  @OA\Property(property="gender", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="Male"),
     *                                  ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="No Data",
     *      ),
     * )
     */


    public function index()
    {
        $branch_id = (new AuthController)->getBranch();

        if(Admin::whereHas('user', function($query) use ($branch_id)
        {
            $query->where('branch_id', $branch_id);
        }
        )->doesntExist())
        {
            return response()->json([
                'status' => false,
            ], 204);
        }

        $admin = Admin::whereHas('user', function($query) use ($branch_id)
        {
            $query->where('branch_id', $branch_id);
        }
        )->with(['user' => function($query) {
            $query->select('id', 'registration_id');
        }
        ])->with('religion', 'gender')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $admin
        ], 200);
    }


    /**
     * @OA\Get(
     *      path="/api/admin/{id}",
     *      summary="Get admin",
     *      security={{"bearerAuth":{}}},
     *      tags={"admin"},
     *      @OA\Parameter(
     *         name="admin_id",
     *         in="path",
     *         description="ID of the admin to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="admin fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="user_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="string", example="Mr. admin"),
     *                                  @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *                                  @OA\Property(property="phone_no", type="string", example="01756439189"),
     *                                  @OA\Property(property="nid_no", type="string", example="56439189"),
     *                                  @OA\Property(property="religion_id", type="integer", example=2),
     *                                  @OA\Property(property="dob", type="date", example="2022-02-02"),
     *                                  @OA\Property(property="gender_id", type="integer", example=2),
     *                                  @OA\Property(property="profile_photo_url", type="string", example="img1.png"),
     *                                  @OA\Property(property="status", type="integer", example=1),
     *                                  @OA\Property(property="user", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="registration_id", type="string", example="0000000"),
     *                                  ),
     *                                  @OA\Property(property="religion", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="Christian"),
     *                                      ),
     *                                  @OA\Property(property="gender", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="Male"),
     *                                  ),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="No admin",
     *      ),
     * )
     */


    public function read($id)
    {
        if(!Admin::find($id))
        {
            return response()->json([], 204);
        }

        $admin = Admin::with(['user' => function($query) {
            $query->select('id', 'registration_id');
        }
        ])->with('religion', 'gender')
            ->find($id);

        return response()->json([
            'status' => true,
            'data' => $admin
        ], 200);
    }


    /**
     * @OA\Get(
     *      path="/api/admin/by_branch/{branch_id}",
     *      summary="Get all admin",
     *      security={{"bearerAuth":{}}},
     *      tags={"admin"},
     *      @OA\Parameter(
     *         name="branch_id",
     *         in="path",
     *         description="ID of the branch to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="All admin fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="user_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="string", example="Mr. admin"),
     *                                  @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *                                  @OA\Property(property="phone_no", type="string", example="01756439189"),
     *                                  @OA\Property(property="nid_no", type="string", example="56439189"),
     *                                  @OA\Property(property="religion_id", type="integer", example=2),
     *                                  @OA\Property(property="dob", type="date", example="2022-02-02"),
     *                                  @OA\Property(property="gender_id", type="integer", example=2),
     *                                  @OA\Property(property="profile_photo_url", type="string", example="img1.png"),
     *                                  @OA\Property(property="status", type="integer", example=1),
     *                                  @OA\Property(property="user", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="registration_id", type="string", example="0000000"),
     *                                  ),
     *                                  @OA\Property(property="religion", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="Christian"),
     *                                      ),
     *                                  @OA\Property(property="gender", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="Male"),
     *                                  ),
     *                  ),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="No data",
     *      ),
     * )
     */


    public function readByBranch($branch_id)
    {
        if(Admin::whereHas('user', function($query) use ($branch_id)
        {
            $query->where('branch_id', $branch_id);
        }
        )->doesntExist())
        {
            return response()->json([
                'status' => false,
            ], 204);
        }

        $admin = Admin::whereHas('user', function($query) use ($branch_id)
        {
            $query->where('branch_id', $branch_id);
        }
        )->with(['user' => function($query) {
            $query->select('id', 'registration_id');
        }
        ])->with('religion', 'gender')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $admin
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/admin",
     *      tags={"admin"},
     *      summary="Create new admin",
     *      description="Create new admin",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"branch_id","name","email","phone_no","nid_no","religion_id","dob","gender_id","profile_photo_url"},
     *              @OA\Property(property="branch_id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Mr. teacher"),
     *              @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *              @OA\Property(property="phone_no", type="string", example="01756439189"),
     *              @OA\Property(property="nid_no", type="string", example="56439189"),
     *              @OA\Property(property="religion_id", type="integer", example=2),
     *              @OA\Property(property="dob", type="date", example="2022-02-02"),
     *              @OA\Property(property="gender_id", type="integer", example=2),
     *              @OA\Property(property="profile_photo_url", type="string", example="img1.png"),
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
     *                     @OA\Property(property="registration_id", type="string", example=0000000),
     *                     @OA\Property(property="password", type="string", example="0000000"),
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
            'branch_id' => 'required|integer',
            'name' => 'required|string|min:5|max:255',
            'email' => 'required|email|unique:admin',
            'phone_no' => 'required|unique:admin',
            'nid_no' => 'required|unique:admin',
            'religion_id' => 'required|integer',
            'dob' => 'required|date',
            'gender_id' => 'required|integer',
            'profile_photo_url' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())
            ], 422);
        }

        DB::beginTransaction();

        try
        {
            $reg_id = $this->generateUniqueID($request->branch_id, 1);

            $user = User::create([
                'role_id' => 1,
                'branch_id' => $request->branch_id,
                'registration_id' => $reg_id,
                'password' => Hash::make($reg_id),
            ]);

            $uid = $user->id;

            $photo = $request->file('profile_photo_url');
            $photo_file = "admin_" . time() . rand(10, 100);
            $photo_path = $photo->storeAs('public/images/admin', $photo_file);
            $photoURL = Storage::url($photo_path);

            $admin = Admin::create([
                'user_id' => $uid,
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'nid_no' => $request->nid_no,
                'religion_id' => $request->religion_id,
                'dob' => $request->dob,
                'gender_id' => $request->gender_id,
                'profile_photo_url' => "http://192.168.68.128:8002" . $photoURL,
            ]);

            DB::commit();

            $data['registration_id'] = $reg_id;
            $data['password'] = $reg_id;

            return response()->json([
                'data' => $data,
                'status' => true,
            ], 201);
        }
        catch(QueryException $ex)
        {
            DB::rollback();

            return response()->json([
                'status' => false,
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'branch_id' => 'required|integer',
            'name' => 'required|string|min:5|max:255',
            'email' => 'required|email|unique:admin,email,'.$id,
            'phone_no' => 'required|unique:admin,phone_no,'.$id,
            'nid_no' => 'required|unique:admin,nid_no,'.$id,
            'religion_id' => 'required|integer',
            'dob' => 'required|date',
            'gender_id' => 'required|integer',
            'profile_photo_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:0,1',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())
            ], 422);
        }
        try
        {
            $admin->name = $request->input('name');
            $admin->email = $request->input('email');
            $admin->phone_no = $request->input('phone_no');
            $admin->nid_no = $request->input('nid_no');
            $admin->religion_id = $request->input('religion_id');
            $admin->dob = $request->input('dob');
            $admin->gender_id = $request->input('gender_id');
            $admin->status = $request->input('status');

            if($request->hasFile('profile_photo_url'))
            {
                if($admin->profile_photo_url)
                {
                    $prev_photo_url = $admin->profile_photo_url;
                    $prev_photo = str_replace('http://192.168.68.128:8002/storage', 'public', $prev_photo_url);
                    Storage::delete($prev_photo);
                }

                $photo = $request->file('profile_photo_url');
                $photo_file = "admin_" . time() . rand(10, 100);
                $photo_path = $photo->storeAs('public/images/admin', $photo_file);
                $photoURL = Storage::url($photo_path);

                $admin->profile_photo_url = "http://192.168.68.128:8002" . $photoURL;
            }

            $admin->save();

            return response()->json([
                'status' => true
            ], 200);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => false
            ], 304);
        }
    }


    /**
     * @OA\Delete(
     *      path="/api/admin/{id}",
     *      tags={"admin"},
     *      summary="Delete an admin",
     *      description="Delete an admin by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the admin",
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
        $admin = Admin::findOrFail($id);
        try
        {
            $admin->delete();

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
