<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

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
}
