<?php

namespace App\Http\Controllers;

use App\Models\AcademicSession;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class AcademicYearController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/academic_year",
     *      summary="Get all academic year",
     *      operationId="academicYearAll",
     *      security={{"bearerAuth":{}}},
     *      tags={"academic year"},
     *
     *      @OA\Response(
     *          response=200,
     *          description="All academic year fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="branch_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="year", example=2020),
     *                                  @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                                  @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *                                  @OA\Property(property="branch", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="main-branch"),
     *                                      ),
     *                  )
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="No academic year found",
     *      ),
     * )
     */


    public function index()
    {
        if(AcademicYear::count() == 0)
        {
            return response()->json([], 204);
        }

        $years = AcademicYear::with(['branch' => function($query){
            return $query->select('id','name');
        }])->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $years], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/academic_year",
     *      operationId="createAcademicYear",
     *      tags={"academic year"},
     *      summary="Create new academic year",
     *      description="Create new academic year along with sessions and return created data",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"branch_id","name","academic_session"},
     *              @OA\Property(property="branch_id", type="integer", example=1),
     *              @OA\Property(property="name", type="year", example=2020),
     *              @OA\Property(property="academic_session", type="array",
     *                              @OA\Items()),
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
     *                     @OA\Property(property="name", type="year", example=2020),
     *                     @OA\Property(property="academic_session_list", type="array", @OA\Items(
     *                                       @OA\Property(property="academic_year_id", type="integer", example=1),
     *                                       @OA\Property(property="name", type="string", example="Midterm"),
     *                                       @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                                       @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *                     )),
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
     *              @OA\Property(property="error", type="array",@OA\Items())
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
            'name' => 'required|unique:academic_year|date_format:Y',
            'academic_session' => 'required|array',
            'academic_session.*' => 'required|string|min:5|max:30',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())
            ], 422);
        }

        $data = json_decode($request->getContent(), true);

        DB::beginTransaction();

        try
        {
            $year = AcademicYear::create([
                'branch_id' => $data['branch_id'],
                'name' => $data['name']
            ]);

            $info['id'] = $year->id;
            $info['name'] = $year->name;

            foreach ($data['academic_session'] as $session)
            {
                $session = AcademicSession::create([
                    'academic_year_id' => $year->id,
                    'name' => $session,
                ]);
                $info['academic_session_list'][] = $session;
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $info
            ], 201);
        }
        catch(QueryException $ex)
        {
            DB::rollback();
            return response()->json([
                'status' => false
            ], 500);
        }

    }


    /**
     * @OA\Get(
     *     path="/api/academic_year/{id}",
     *     summary="Get a single academic year",
     *     description="Retrieve a single academic year by ID",
     *     tags={"academic year"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the academic year to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Successful operation",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property( property="branch_id", type="integer", example=1),
 *                     @OA\Property( property="name", type="year", example=2022),
 *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
 *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
 *                     @OA\Property(
 *                          property="branch",
 *                          type="object",
*                               @OA\Property(property="id", type="integer", example=1),
*                               @OA\Property(property="name", type="string", example="main-branch"),
*                         ),
     *             )
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="204",
     *         description="Branch not found",
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
        if(!AcademicYear::find($id))
        {
            return response()->json([], 204);
        }

        $year = AcademicYear::with(['branch' => function($query){
            return $query->select('id','name');
        }])->find($id);

        return response()->json([
            'status' => true,
            'data' => $year
        ]);
    }


    /**
     * @OA\Get(
     *      path="/api/academic_year/by_branch/{branch_id}",
     *      summary="Get all academic year under one branch",
     *      operationId="academicYearByBranch",
     *      security={{"bearerAuth":{}}},
     *      tags={"academic year"},
     *      @OA\Parameter(
     *         name="branch_id",
     *         in="path",
     *         description="ID of the branch to retrieve academic years under it",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="All academic year under one branch fetched",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="array",
     *                  @OA\Items(
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="branch_id", type="integer", example=2),
     *                                  @OA\Property(property="name", type="year", example=2020),
     *                                  @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                                  @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *                                  @OA\Property(property="branch", type="object",
     *                                          @OA\Property(property="id", type="integer", example=2),
     *                                          @OA\Property(property="name", type="string", example="sub-branch"),
     *                                      ),
     *                  )
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=204,
     *          description="No academic year under this branch found",
     *      ),
     * )
     */


    public function readByBranch($branch_id)
    {
        if(AcademicYear::where('branch_id', $branch_id)->doesntExist())
        {
            return response()->json([], 204);
        }
        $years = AcademicYear::with(['branch' => function($query){
            return $query->select('id','name');
        }])->where('branch_id', $branch_id)->get();

        return response()->json([
            'status' => true,
            'data' => $years
        ], 200);
    }


    /**
     * @OA\Put(
     *      path="/api/academic_year/{id}",
     *      summary="Update a academic year",
     *      description="Update a academic year by id",
     *      operationId="updateAcademicYear",
     *      tags={"academic year"},
     *      @OA\Parameter(
     *          name="id",
     *          description="academic year ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *
     *      @OA\RequestBody(
     *          description="Academic year object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name","branch_id"},
     *              @OA\Property(property="name", type="year", example=2020),
     *              @OA\Property(property="branch_id", type="integer", example=2),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Academic year updated successfully",
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
     *                     @OA\Property( property="branch_id", type="integer", example=2),
     *                     @OA\Property( property="name", type="year", example=2020),
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
        $year = AcademicYear::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'branch_id' => 'required|integer',
            'name' => 'required|date_format:Y'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $year->update([
                'name' => $request->name,
                'branch_id' => $request->branch_id
            ]);

            return response()->json([
                'status' => true,
                'data' => $year
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
     *      path="/api/academic_year/{id}",
     *      summary="Delete an academic year",
     *      operationId="academicYearDelete",
     *      security={{"bearerAuth":{}}},
     *      tags={"academic year"},
     *
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the academic year to delete",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="academic year deleted",
     *          @OA\JsonContent(type="object",
     *              @OA\Property(property="status", type="boolean", example=true),
     *              ),
     *          ),
     *      ),
     *
     *      @OA\Response(
     *          response=304,
     *          description="database error",
     *      ),
     * )
     */


    public function delete($id)
    {
        $year = AcademicYear::findOrFail($id);
        try
        {
            $year->delete();

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

    public function restore($id)
    {
        AcademicYear::where('id', $id)->withTrashed()->restore();

        return response()->json([
            'status' => true], 200);
    }

    public function restoreAll()
    {
        AcademicYear::onlyTrashed()->restore();

        return response()->json([
            'status' => true], 200);
    }

    public function forceDelete($id)
    {
        AcademicYear::where('id', $id)->withTrashed()->forceDelete();

        return response()->json([
            'status' => true], 200);
    }
}
