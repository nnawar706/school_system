<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AcademicSessionController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/academic_session/{id}",
     *     summary="Get a single academic session",
     *     operationId="academicSessionRead",
     *     security={{"bearerAuth":{}}},
     *     tags={"academic session"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the academic session to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="Retrieve a single academic session by ID",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="academic_year_id", type="integer", example=13),
     *                     @OA\Property(property="name", type="string", example="Half-yearly"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="academic_year", type="object",
     *                                  @OA\Property(property="id", type="integer", example=13),
     *                                  @OA\Property(property="name", type="year", example=2010)
     *                     ),
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
     *
     *     @OA\Response(
     *         response="204",
     *         description="Academic session not found",
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
        if(!AcademicSession::find($id))
        {
            return response()->json([], 204);
        }

        $session = AcademicSession::with(['academic_year' => function($query){
            return $query->select('id','name');
        }])->find($id);

        return response()->json([
            'status' => true,
            'data' => $session
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/academic_session/by_year/{academic_year_id}",
     *     summary="Get all academic session under one academic year",
     *     operationId="academicSessionByYear",
     *     security={{"bearerAuth":{}}},
     *     tags={"academic session"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all academic session under one academic year",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="academic_year_id", type="integer", example=13),
     *                     @OA\Property(property="name", type="string", example="Half-yearly"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="academic_year", type="object",
     *                                  @OA\Property(property="id", type="integer", example=13),
     *                                  @OA\Property(property="name", type="year", example=2010)
     *                     )),
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


    public function readByYear($academic_year_id)
    {
        if(AcademicSession::where('academic_year_id', $academic_year_id)->doesntExist())
        {
            return response()->json([], 204);
        }

        $sessions = AcademicSession::with(['academic_year' => function($query) {
            return $query->select('id', 'name');
        }])->where('academic_year_id', $academic_year_id)->get();

        return response()->json([
            'status' => true,
            'data' => $sessions
        ], 200);
    }


    /**
     * @OA\Put(
     *      path="/api/academic_session/{id}",
     *      summary="Update a academic session",
     *      description="Update a academic session by id",
     *      operationId="updateAcademicSession",
     *      tags={"academic session"},
     *      @OA\Parameter(
     *          name="id",
     *          description="academic session ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          description="Academic session object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","academic_year_id"},
     *              @OA\Property(property="name", type="string", example="final"),
     *              @OA\Property(property="academic_year_id", type="integer", example=2),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Academic session updated successfully",
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
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="academic_year_id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="final"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="branch", type="object",
     *                                          @OA\Property(property="id", type="integer", example=2),
     *                                          @OA\Property(property="name", type="string", example="sub-branch"),
     *                                      ),
     *             )
     *              )
     *          )
     *      ),
     * )
     */


    public function update(Request $request, $id)
    {
        $session = AcademicSession::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'academic_year_id' => 'required|integer',
            'name' => 'required|string|min:5|max:30'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $session->update([
                'name' => $request->name,
                'academic_year_id' => $request->academic_year_id
            ]);

            return response()->json([
                'status' => true,
                'data' => $session
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
     *      path="/api/academic_session/{id}",
     *      summary="Delete an academic session",
     *      operationId="academicSessionDelete",
     *      security={{"bearerAuth":{}}},
     *      tags={"academic session"},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the academic session to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="academic session deleted",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="status", type="boolean", example=true),
     *              ),
     *          ),
     *      ),
     *
     * )
     */


    public function delete($id)
    {
        $session = AcademicSession::findOrFail($id);
        try
        {
            $session->delete();

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
