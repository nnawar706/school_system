<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\ClassHasSubject;
use App\Models\LibraryShelf;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BatchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/class",
     *     summary="Get all class",
     *     tags={"class"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all class",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Astrophysics"),
     *                     @OA\Property(property="branch", type="object",
     *                              @OA\Property(property="id", type="integer", example=1),
     *                              @OA\Property(property="name", type="string", example="main branch"),
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
     *     @OA\Response(
     *          response="204",
     *          description="No data",
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

        if(!Batch::where('branch_id', $branch_id)->exists())
        {
            return response()->json([], 204);
        }

        $class = Batch::with(['branch' => function($query)
        {
            return $query->select('id','name');
        }
        ])->where('branch_id', $branch_id)->get();

        return response()->json([
            'status' => true,
            'data' => $class
        ], 200);
    }


    /**
     * @OA\Get(
     *     path="/api/class/by_branch/{branch_id}",
     *     summary="Get all class",
     *     tags={"class"},
     *     @OA\Parameter(
     *          name="id",
     *          description="branch ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all class under one branch",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Astrophysics"),
     *                     @OA\Property(property="branch", type="object",
     *                              @OA\Property(property="id", type="integer", example=1),
     *                              @OA\Property(property="name", type="string", example="main branch"),
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
     *
     *     @OA\Response(
     *          response="204",
     *          description="No data",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="status", type="boolean", example=false)
     *          )
     *      ),
     * )
     */


    public function readByBranch($branch_id)
    {
        if(Batch::where('branch_id', $branch_id)->doesntExist())
        {
            return response()->json([], 204);
        }
        $class = Batch::with(['branch' => function($query)
        {
            return $query->select('id','name');
        }
        ])->where('branch_id', $branch_id)->get();

        return response()->json([
            'status' => true,
            'data' => $class
        ], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/class",
     *      operationId="createclass",
     *      tags={"class"},
     *      summary="Create new class",
     *      description="Create new class with its subjects",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name", "subject_list"},
     *              @OA\Property(property="name", type="string", example="prep one"),
     *              @OA\Property(property="subject_list", type="array", @OA\Items(type="integer")
     *              ),
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
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="prep one"),
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
            'name' => ['required','max:30','min:5','string',
                Rule::unique('class')->where(function ($query) use ($branch_id, $request) {
                    return $query->where('branch_id', $branch_id);
                })
            ],
            'subject_list' => 'required|array',
            'subject_list.*' => 'required|integer|distinct',
        ], [
            'subject_list.*.distinct' => 'Duplicate subjects are not allowed.',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }

        $data = json_decode($request->getContent(), true);

        DB::beginTransaction();

        try
        {
            $class = Batch::create([
                'branch_id' => $branch_id,
                'name' => $data['name']
            ]);

            foreach ($data['subject_list'] as $subject)
            {
                if(ClassHasSubject::where('class_id', $class->id)->where('subject_id', $subject)->exists())
                {
                    DB::rollback();

                    return response()->json([
                        'error' => ["Given subject already exists in this class"]
                    ], 422);
                }

                ClassHasSubject::create([
                    'class_id' => $class->id,
                    'subject_id' => $subject
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
            ], 201);
        }
        catch (QueryException $ex)
        {
            DB::rollback();
            return response()->json([
                'status' => false], 500);
        }
    }


    /**
     * @OA\Put(
     *      path="/api/class/{id}",
     *      summary="Update a class",
     *      description="Update a class by id",
     *      operationId="updateclass",
     *      tags={"class"},
     *      @OA\Parameter(
     *          name="id",
     *          description="class ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="class object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"name"},
     *              @OA\Property(property="name", type="string", example="prep-two"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="class updated successfully",
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
     *                     @OA\Property(property="branch_id", type="integer", example="1", description="Branch ID"),
     *                     @OA\Property(property="name", type="string", example="main-branch", description="prep two"),
     *             )
     *              )
     *          )
     *      ),
     * )
     */

    public function update(Request $request, $id)
    {
        $class = Batch::with('subject_list')->findOrFail($id);

        $branch_id = (new AuthController)->getBranch();

        $validate = Validator::make($request->all(), [
            'name' => ['required','max:30','min:5','string',
                Rule::unique('class')->where(function ($query) use ($branch_id, $request) {
                    return $query->where('branch_id', $branch_id);
                })->ignore($id)
            ],
            'subject_list' => 'required|array',
            'subject_list.*' => 'required|integer|distinct',
        ], [
            'subject_list.*.distinct' => 'Duplicate subjects are not allowed.',
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }

        $data = json_decode($request->getContent(), true);

        DB::beginTransaction();

        try {
            $class->subject_list()->delete();

            $class->update([
                'branch_id' => $branch_id,
                'name' => $request->name,
            ]);

            foreach ($data['subject_list'] as $subject)
            {
                if(ClassHasSubject::where('class_id', $class->id)->where('subject_id', $subject)->exists())
                {
                    DB::rollback();

                    return response()->json([
                        'error' => ["Given subject already exists in this class"]
                    ], 422);
                }

                ClassHasSubject::create([
                    'class_id' => $class->id,
                    'subject_id' => $subject
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
            ], 200);
        }
        catch(QueryException $ex)
        {
            DB::rollback();

            return response()->json([
                'status' => false], 304);
        }
    }


    /**
     * @OA\Delete(
     *      path="/api/class/{id}",
     *      operationId="deleteClass",
     *      tags={"class"},
     *      summary="Delete a class",
     *      description="Delete a class by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the class",
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
        DB::transaction(function () use ($id)
        {
            $class = Batch::with('subject_list')->findOrFail($id);

            try
            {
                $class->subject_list()->delete();
                $class->delete();

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
        });
    }
}
