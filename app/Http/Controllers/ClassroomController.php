<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Classroom;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassroomController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/classroom",
     *     summary="Get all classroom",
     *     tags={"classroom"},
     *     description="Get all classroom under the logged in admin's branch",
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all classroom",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="01022001"),
     *                     @OA\Property(property="max_student", type="integer", example=30),
     *                     @OA\Property(property="student_quantity", type="integer", example=21),
     *                     @OA\Property(property="active_status", type="integer", example=1),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="branch", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="main-branch"),
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
        $result = (new AuthController)->me();

        $branch_id = $result->original['data']['branch_id'];

        if(Classroom::where('branch_id', $branch_id)->doesntExist())
        {
            return response()->json([], 204);
        }

        $classrooms = Classroom::with(['branch' => function($query)
            {
                return $query->select('id','name');
            }
        ])->where('branch_id', $branch_id)->get();

        return response()->json([
                'status' => true,
                'data' => $classrooms
        ], 200);
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

}
