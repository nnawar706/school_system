<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{

    /**
     * @OA\Get(
     *      path="/api/teacher",
     *      summary="Get all teacher",
     *      operationId="teacherAll",
     *      security={{"bearerAuth":{}}},
     *      tags={"teacher"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="All teacher fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="user_id", type="integer", example=2),
     *                                  @OA\Property(property="designation_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="string", example="Mr. teacher"),
     *                                  @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *                                  @OA\Property(property="phone_no", type="string", example="01756439189"),
     *                                  @OA\Property(property="nid_no", type="string", example="56439189"),
     *                                  @OA\Property(property="religion_id", type="integer", example=2),
     *                                  @OA\Property(property="expertise_subject_id", type="integer", example=2),
     *                                  @OA\Property(property="dob", type="date", example="2022-02-02"),
     *                                  @OA\Property(property="gender_id", type="integer", example=2),
     *                                  @OA\Property(property="salary", type="integer", example=250000),
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
     *                                  @OA\Property(property="expertise_subject", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="English"),
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
     *          description="No teacher",
     *      ),
     * )
     */


    public function index()
    {
        $branch_id = (new AuthController)->getBranch();

        if(Teacher::whereHas('user', function($query) use ($branch_id)
        {
            $query->where('branch_id', $branch_id);
        }
        )->doesntExist())
        {
            return response()->json([
                'status' => false,
            ], 204);
        }

        $teacher = Teacher::whereHas('user', function($query) use ($branch_id)
        {
            $query->where('branch_id', $branch_id);
        }
        )->with(['user' => function($query) {
            $query->select('id', 'registration_id');
        }
        ])->with('religion', 'designation', 'expertise_subject', 'gender')
        ->latest()
        ->get();

        return response()->json([
            'status' => true,
            'data' => $teacher
        ], 200);
    }


    /**
     * @OA\Get(
     *      path="/api/teacher/{id}",
     *      summary="Get teacher",
     *      operationId="teacherOne",
     *      security={{"bearerAuth":{}}},
     *      tags={"teacher"},
     *      @OA\Parameter(
     *         name="teacher_id",
     *         in="path",
     *         description="ID of the teacher to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="teacher fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="user_id", type="integer", example=2),
     *                                  @OA\Property(property="designation_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="string", example="Mr. teacher"),
     *                                  @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *                                  @OA\Property(property="phone_no", type="string", example="01756439189"),
     *                                  @OA\Property(property="nid_no", type="string", example="56439189"),
     *                                  @OA\Property(property="salary", type="integer", example=250000),
     *                                  @OA\Property(property="religion_id", type="integer", example=2),
     *                                  @OA\Property(property="expertise_subject_id", type="integer", example=2),
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
     *                                  @OA\Property(property="expertise_subject", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="English"),
     *                                  ),
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
     *          description="No teacher",
     *      ),
     * )
     */


    public function read($id)
    {
        if(!Teacher::find($id))
        {
            return response()->json([], 204);
        }

        $teacher = Teacher::with(['user' => function($query) {
            $query->select('id', 'registration_id');
        }
        ])->with('religion', 'designation', 'expertise_subject', 'gender')
            ->find($id);

        return response()->json([
            'status' => true,
            'data' => $teacher
        ], 200);
    }


    /**
     * @OA\Get(
     *      path="/api/teacher/by_branch/{branch_id}",
     *      summary="Get all teacher",
     *      operationId="teacherbranchAll",
     *      security={{"bearerAuth":{}}},
     *      tags={"teacher"},
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
     *          description="All teacher fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="user_id", type="integer", example=2),
     *                                  @OA\Property(property="designation_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="string", example="Mr. teacher"),
     *                                  @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *                                  @OA\Property(property="phone_no", type="string", example="01756439189"),
     *                                  @OA\Property(property="nid_no", type="string", example="56439189"),
     *                                  @OA\Property(property="religion_id", type="integer", example=2),
     *                                  @OA\Property(property="expertise_subject_id", type="integer", example=2),
     *                                  @OA\Property(property="dob", type="date", example="2022-02-02"),
     *                                  @OA\Property(property="gender_id", type="integer", example=2),
     *                                  @OA\Property(property="salary", type="integer", example=250000),
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
     *                                  @OA\Property(property="expertise_subject", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="English"),
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
     *          description="No teacher",
     *      ),
     * )
     */


    public function readByBranch($branch_id)
    {
        if(Teacher::whereHas('user', function($query) use ($branch_id)
        {
            $query->where('branch_id', $branch_id);
        }
        )->doesntExist())
        {
            return response()->json([
                'status' => false,
            ], 204);
        }

        $teacher = Teacher::whereHas('user', function($query) use ($branch_id)
        {
            $query->where('branch_id', $branch_id);
        }
        )->with(['user' => function($query) {
            $query->select('id', 'registration_id');
        }
        ])->with('religion', 'designation', 'expertise_subject', 'gender')
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $teacher
        ], 200);
    }


    /**
     * @OA\Get(
     *      path="/api/teacher/by_expertise/{subject_id}",
     *      summary="Get all teacher with similar expertise",
     *      operationId="teacherAllExpertise",
     *      security={{"bearerAuth":{}}},
     *      tags={"teacher"},
     *      @OA\Parameter(
     *         name="subject_id",
     *         in="path",
     *         description="ID of the subject to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="All teacher fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="user_id", type="integer", example=2),
     *                                  @OA\Property(property="designation_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="string", example="Mr. teacher"),
     *                                  @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *                                  @OA\Property(property="phone_no", type="string", example="01756439189"),
     *                                  @OA\Property(property="nid_no", type="string", example="56439189"),
     *                                  @OA\Property(property="religion_id", type="integer", example=2),
     *                                  @OA\Property(property="expertise_subject_id", type="integer", example=2),
     *                                  @OA\Property(property="dob", type="date", example="2022-02-02"),
     *                                  @OA\Property(property="gender_id", type="integer", example=2),
     *                                  @OA\Property(property="salary", type="integer", example=250000),
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
     *                                  @OA\Property(property="expertise_subject", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="English"),
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
     *          description="No teacher",
     *      ),
     * )
     */


    public function readByExpertise($subject_id)
    {
        if(!Teacher::where('expertise_subject_id', $subject_id)->exists())
        {
            return response()->json([], 204);
        }

        $teacher = Teacher::with(['user' => function($query) {
            $query->select('id', 'registration_id');
        }
        ])->with('religion', 'designation', 'expertise_subject', 'gender')
            ->where('expertise_subject_id', $subject_id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $teacher
        ], 200);
    }


    /**
     * @OA\Get(
     *      path="/api/teacher/by_designation/{designation_id}",
     *      summary="Get all teacher with similar designation",
     *      operationId="teacherAlldesignation",
     *      security={{"bearerAuth":{}}},
     *      tags={"teacher"},
     *      @OA\Parameter(
     *         name="designation_id",
     *         in="path",
     *         description="ID of the designation to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="All teacher fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="user_id", type="integer", example=2),
     *                                  @OA\Property(property="designation_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="string", example="Mr. teacher"),
     *                                  @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *                                  @OA\Property(property="phone_no", type="string", example="01756439189"),
     *                                  @OA\Property(property="nid_no", type="string", example="56439189"),
     *                                  @OA\Property(property="religion_id", type="integer", example=2),
     *                                  @OA\Property(property="expertise_subject_id", type="integer", example=2),
     *                                  @OA\Property(property="dob", type="date", example="2022-02-02"),
     *                                  @OA\Property(property="gender_id", type="integer", example=2),
     *                                  @OA\Property(property="salary", type="integer", example=250000),
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
     *                                  @OA\Property(property="expertise_subject", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="English"),
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
     *          description="No teacher",
     *      ),
     * )
     */


    public function readByDesignation($designation_id)
    {
        if(!Teacher::where('designation_id', $designation_id)->exists())
        {
            return response()->json([], 204);
        }

        $teacher = Teacher::with(['user' => function($query) {
            $query->select('id', 'registration_id');
        }
        ])->with('religion', 'designation', 'expertise_subject', 'gender')
            ->where('designation_id', $designation_id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $teacher
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/teacher",
     *      operationId="createteacher",
     *      tags={"teacher"},
     *      summary="Create new teacher",
     *      description="Create new teacher",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"designation_id","name","email","phone_no","nid_no","religion_id","expertise_subject_id","dob","gender_id","profile_photo_url"},
     *              @OA\Property(property="designation_id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Mr. teacher"),
     *              @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *              @OA\Property(property="phone_no", type="string", example="01756439189"),
     *              @OA\Property(property="nid_no", type="string", example="56439189"),
     *              @OA\Property(property="salary", type="integer", example=250000),
     *              @OA\Property(property="religion_id", type="integer", example=2),
     *              @OA\Property(property="expertise_subject_id", type="integer", example=2),
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
     *                     @OA\Property(property="registration_id", type="integer", example=0000000),
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
            'designation_id' => 'required|integer',
            'name' => 'required|string|min:5|max:255',
            'email' => 'required|email|unique:teacher',
            'phone_no' => 'required|unique:teacher',
            'nid_no' => 'required|unique:teacher',
            'salary' => 'required|integer',
            'religion_id' => 'required|integer',
            'expertise_subject_id' => 'required|integer',
            'dob' => 'required|date',
            'gender_id' => 'required|integer',
            'profile_photo_url' => 'required|image|unique:teacher|mimes:jpeg,png,jpg|max:2048'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())
            ], 422);
        }

        $branch_id = (new AuthController)->getBranch();

        DB::beginTransaction();

        try
        {
            $reg_id = $this->generateUniqueID($branch_id, 2);

            $user = User::create([
                'role_id' => 2,
                'branch_id' => $branch_id,
                'registration_id' => $reg_id,
                'password' => Hash::make($reg_id),
            ]);

            $uid = $user->id;

            $photo = $request->file('profile_photo_url');
            $photo_file = "teacher_" . time() . rand(10, 100);
            $photo_path = $photo->storeAs('public/images/teacher', $photo_file);
            $photoURL = Storage::url($photo_path);

            $teacher = Teacher::create([
                'user_id' => $uid,
                'designation_id' => $request->designation_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'nid_no' => $request->nid_no,
                'salary' => $request->salary,
                'religion_id' => $request->religion_id,
                'expertise_subject_id' => $request->expertise_subject_id,
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


    /**
     * @OA\Post(
     *      path="/api/teacher/{id}",
     *      operationId="updateteacher",
     *      tags={"teacher"},
     *      @OA\Parameter(
     *          name="id",
     *          description="teacher ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      summary="Update teacher",
     *      description="Update teacher",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"designation_id","name","email","phone_no","nid_no","religion_id","expertise_subject_id","dob","gender_id","profile_photo_url","status"},
     *              @OA\Property(property="designation_id", type="integer", example=1),
     *              @OA\Property(property="name", type="string", example="Mr. teacher"),
     *              @OA\Property(property="email", type="string", example="teacher@gmail.com"),
     *              @OA\Property(property="phone_no", type="string", example="01756439189"),
     *              @OA\Property(property="nid_no", type="string", example="56439189"),
     *              @OA\Property(property="salary", type="integer", example=250000),
     *              @OA\Property(property="religion_id", type="integer", example=2),
     *              @OA\Property(property="expertise_subject_id", type="integer", example=2),
     *              @OA\Property(property="dob", type="date", example="2022-02-02"),
     *              @OA\Property(property="gender_id", type="integer", example=2),
     *              @OA\Property(property="profile_photo_url", type="string", example="img1.png"),
     *              @OA\Property(property="status", type="integer", example=0),
     *          ),
     *      ),
     *      @OA\Response(
     *          response="200",
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
     *                     @OA\Property(property="registration_id", type="integer", example=0000000),
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


    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'designation_id' => 'required|integer',
            'name' => 'required|string|min:5|max:255',
            'email' => 'required|email|unique:teacher,email,'.$id,
            'phone_no' => 'required|unique:teacher,phone_no,'.$id,
            'nid_no' => 'required|unique:teacher,nid_no,'.$id,
            'salary' => 'required|integer',
            'religion_id' => 'required|integer',
            'expertise_subject_id' => 'required|integer',
            'dob' => 'required|date',
            'gender_id' => 'required|integer',
            'status' => 'required|in:0,1',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }

        try
        {
            $teacher->designation_id = $request->input('designation_id');
            $teacher->name = $request->input('name');
            $teacher->email = $request->input('email');
            $teacher->phone_no = $request->input('phone_no');
            $teacher->nid_no = $request->input('nid_no');
            $teacher->salary = $request->input('salary');
            $teacher->religion_id = $request->input('religion_id');
            $teacher->expertise_subject_id = $request->input('expertise_subject_id');
            $teacher->dob = $request->input('dob');
            $teacher->gender_id = $request->input('gender_id');

            if($request->hasFile('profile_photo_url'))
            {
                if($teacher->profile_photo_url)
                {
                    $prev_photo_url = $teacher->profile_photo_url;
                    $prev_photo = str_replace('http://192.168.68.128:8002', '', $prev_photo_url);
                    Storage::delete($prev_photo);
                }

                $photo = $request->file('profile_photo_url');
                $photo_file = "teacher_" . time() . rand(10, 100);
                $photo_path = $photo->storeAs('public/images/teacher', $photo_file);
                $photoURL = Storage::url($photo_path);

                $teacher->photo_url = "http://192.168.68.128:8002" . $photoURL;
            }

            $teacher->save();

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
     *      path="/api/teacher/{id}",
     *      operationId="deleteteacher",
     *      tags={"teacher"},
     *      summary="Delete a teacher",
     *      description="Delete a teacher by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the teacher",
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
        $teacher = Teacher::findOrFail($id);
        try
        {
            $teacher->delete();

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
