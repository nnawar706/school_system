<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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
        $branch_id = (new AuthController)->getBranch();

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
     *      path="/api/classroom",
     *      operationId="createClassroom",
     *      tags={"classroom"},
     *      summary="Create new classroom",
     *      description="Create new classroom and return created data (classroom name format:[first two digits-branch no,
            next two digits-floor no, next two digits-room no]->regex:/^(10|0[1-9])\d{2}(0[1-9]|[1-9][0-9])$/)",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"branch_id","name","max_student"},
     *              @OA\Property(property="name", type="string", example="010220"),
     *              @OA\Property(property="max_student", type="integer", example=35),
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
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="010220"),
     *                     @OA\Property(property="max_student", type="integer", example=35),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="id", type="integer", example=1),
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
        $branch_id = (new AuthController)->getBranch();

        $validate = Validator::make($request->all(), [
            'name' => ['required', 'unique:classroom', 'max:6', 'string',
                'regex:/^(10|0[1-9])\d{2}(0[1-9]|[1-9][0-9])$/'],
            'max_student' => 'required|integer|max:50|min:20'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $classroom = Classroom::create([
                'branch_id' => $branch_id,
                'name' => $request->name,
                'max_student' => $request->max_student
            ]);

            return response()->json([
                'status' => true,
                'data' => $classroom
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
     *      path="/api/classroom/{id}",
     *      summary="Update a classroom",
     *      description="Update a classroom by id",
     *      operationId="updateclassroom",
     *      tags={"classroom"},
     *      @OA\Parameter(
     *          name="id",
     *          description="classroom ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="classroom object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","max_student","active_status"},
     *              @OA\Property(property="name", type="string", example="010220"),
     *              @OA\Property(property="max_student", type="integer", example=35),
     *              @OA\Property(property="active_status", type="integer", example=0),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="classroom updated successfully",
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
     *                     @OA\Property(property="branch_id", type="integer", example="1"),
     *                     @OA\Property(property="name", type="string", example="010220"),
     *                     @OA\Property(property="max_student", type="integer", example=35),
     *                     @OA\Property(property="active_status", type="integer", example=0),
     *             )
     *              )
     *          )
     *      )
     * )
     */


    public function update(Request $request, $id)
    {
        $room = Classroom::findOrFail($id);

        $branch_id = (new AuthController)->getBranch();

        $validate = Validator::make($request->all(), [
            'name' => ['required', 'max:6', 'string',
                'regex:/^(10|0[1-9])\d{2}(0[1-9]|[1-9][0-9])$/',
                Rule::unique('classroom')->ignore($id),],
            'max_student' => 'required|integer|max:50|min:20',
            'active_status' => 'required|in:0,1',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $room->update([
                'branch_id' => $branch_id,
                'name' => $request->name,
                'max_student' => $request->max_student,
                'active_status' => $request->active_status,
            ]);

            return response()->json([
                'status' => true,
                'data' => $room
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
     *      path="/api/classroom/{id}",
     *      operationId="deleteClassroom",
     *      tags={"classroom"},
     *      summary="Delete a classroom",
     *      description="Delete a classroom by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the classroom",
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
        $room = Classroom::findOrFail($id);
        try
        {
            $room->delete();

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
