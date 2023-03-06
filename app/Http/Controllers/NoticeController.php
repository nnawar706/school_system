<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class NoticeController extends Controller
{


    public function index()
    {
        $branch_id = (new AuthController)->getBranch();

        if(Notice::where('branch-id', $branch_id)->doesntExist())
        {
            return response()->json([], 204);
        }

        $notice = Notice::with(
                ['notice_type' => function($query)
                {
                    return $query->select('id', 'name');
                }])
            ->with(
                ['branch' => function($query)
                {
                    return $query->select('id', 'name');
                }])
            ->where('branch_id', $branch_id)
            ->latest()
            ->take(20)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $notice], 200);
    }


    /**
     * @OA\Post(
     *      path="/api/notice",
     *      operationId="createNotice",
     *      tags={"notice"},
     *      summary="Create new notice",
     *      description="Create new notice and return created data",
     *      security={{"bearerAuth": {}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"branch_id","notice_type_id","title","details"},
     *              @OA\Property(property="branch_id", type="integer", example=1),
     *              @OA\Property(property="notice_type_id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="Public Holiday"),
     *              @OA\Property(property="details", type="string", example="There will be no class on 21st February."),
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
     *                     @OA\Property(property="notice_type_id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Public Holiday"),
     *                     @OA\Property(property="details", type="string", example="There will be no class on 21st February."),
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
            'notice_type_id' => 'required|integer',
            'title' => 'required|max:255|min:5|string',
            'details' => 'required|string|min:10'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $notice = Notice::create([
                'branch_id' => $branch_id,
                'notice_type_id' => $request->notice_type_id,
                'title' => $request->title,
                'details' => $request->details,
            ]);

            return response()->json([
                'status' => true,
                'data' => $notice
            ], 201);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => false], 500);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/notice/{id}",
     *     summary="Get a single notice",
     *     description="Retrieve a single notice by ID",
     *     tags={"notice"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the notice to retrieve",
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
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="notice_type_id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Public Holiday"),
     *                     @OA\Property(property="details", type="string", example="There will be no class on 21st February."),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="notice_type", type="object",
     *                                  @OA\Property(property="id", type="integer", example="1"),
     *                                  @OA\Property(property="name", type="string", example="General"),
     *                     ),
     *                     @OA\Property(property="branch", type="object",
     *                                  @OA\Property(property="id", type="integer", example=1),
     *                                  @OA\Property(property="name", type="string", example="main-branch"),
     *                     ),
     *             )
     *         )
     *     ),
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
        if($notice = Notice::with(['notice_type' => function($query)
            {
                return $query->select('id', 'name');
            }])
            ->with(['branch' => function($query)
                {
                    return $query->select('id', 'name');
                }])
            ->find($id))
        {
            return response()->json([
                'status' => true,
                'data' => $notice
            ]);
        }
        return response()->json([], 204);
    }


    /**
     * @OA\Get(
     *     path="/api/notice/by_notice_type/{notice_type_id}",
     *     summary="Get all notice by type",
     *     tags={"notice"},
     *     description="Get all notice under one type",
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all notice",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="notice_type_id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Public Holiday"),
     *                     @OA\Property(property="details", type="string", example="there will be no classes on 14th April since it's the Bangla new year. Enjoy!"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="notice_type", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="General"),
     *                     ),
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
     *          response="204",
     *          description="No data",
     *      ),
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


    public function readByType($notice_type_id)
    {
        if(Notice::where('notice_type_id', $notice_type_id)->doesntExist())
        {
            return response()->json([], 204);
        }

        $notice = Notice::with(['notice_type' => function($query)
        {
            return $query->select('id', 'name');
        }])
        ->with(['branch' => function($query)
        {
            return $query->select('id', 'name');
        }])
        ->where('notice_type_id', $notice_type_id)
        ->get();

        return response()->json([
            'status' => true,
            'data' => $notice
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/notice/by_branch/{branch_id}",
     *     summary="Get all notice by branch",
     *     tags={"notice"},
     *     description="Get all notice under one branch",
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all notice",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="notice_type_id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Public Holiday"),
     *                     @OA\Property(property="details", type="string", example="there will be no classes on 14th April since it's the Bangla new year. Enjoy!"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="notice_type", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="General"),
     *                     ),
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
     *          response="204",
     *          description="No data",
     *      ),
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


    public function readByBranch($branch_id)
    {
        if(Notice::where('branch_id', $branch_id)->doesntExist())
        {
            return response()->json([], 204);
        }

        $notice = Notice::with(['notice_type' => function($query)
        {
            return $query->select('id', 'name');
        }])
            ->with(['branch' => function($query)
            {
                return $query->select('id', 'name');
            }])
            ->where('branch_id', $branch_id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $notice
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/notice/by_branch/by_type/{branch_id}/{type_id}",
     *     summary="Get all notice under one branch by type",
     *     tags={"notice"},
     *     description="Get all notice under one type and one branch",
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all notice",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="branch_id", type="integer", example=1),
     *                     @OA\Property(property="notice_type_id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="Public Holiday"),
     *                     @OA\Property(property="details", type="string", example="there will be no classes on 14th April since it's the Bangla new year. Enjoy!"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="notice_type", type="object",
     *                                          @OA\Property(property="id", type="integer", example=1),
     *                                          @OA\Property(property="name", type="string", example="General"),
     *                     ),
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
     *          response="204",
     *          description="No data",
     *      ),
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


    public function readByBranchAndType($branch_id, $type_id)
    {
        if(Notice::where('branch_id', $branch_id)->where('notice_type_id', $type_id)->doesntExist())
        {
            return response()->json([], 204);
        }

        $notice = Notice::with(['notice_type' => function($query)
        {
            return $query->select('id', 'name');
        }])
            ->with(['branch' => function($query)
            {
                return $query->select('id', 'name');
            }])
            ->where('branch_id', $branch_id)
            ->where('notice_type_id', $type_id)
            ->get();

        return response()->json([
            'status' => true,
            'data' => $notice
        ]);
    }


    /**
     * @OA\Put(
     *      path="/api/notice/{id}",
     *      summary="Update a notice",
     *      description="Update a notice by id",
     *      operationId="updateNotice",
     *      tags={"notice"},
     *      @OA\Parameter(
     *          name="id",
     *          description="Notice ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer",
     *              format="int64"
     *          )
     *      ),
     *      @OA\RequestBody(
     *          description="Notice object that needs to be updated",
     *          required=true,
     *          @OA\JsonContent(
     *              required={"branch_id","notice_type_id","title","details"},
     *              @OA\Property(property="branch_id", type="integer", example=2),
     *              @OA\Property(property="notice_type_id", type="integer", example=2),
     *              @OA\Property(property="title", type="string", example="Public Holiday"),
     *              @OA\Property(property="details", type="string", example="There will be no class on 21st February."),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Notice updated successfully",
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
     *                     @OA\Property(property="id", type="integer", example="1", description="Branch ID"),
     *                     @OA\Property(property="branch_id", type="integer", example=2),
     *                     @OA\Property(property="notice_type_id", type="integer", example=2),
     *                     @OA\Property(property="title", type="string", example="Public Holiday"),
     *                     @OA\Property(property="details", type="string", example="There will be no class on 21st February."),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *             )
     *              )
     *          )
     *      ),
     * )
     */

    public function update(Request $request, $id)
    {
        $notice = Notice::findOrFail($id);

        $validate = Validator::make($request->all(), [
            'notice_type_id' => 'required|integer',
            'title' => 'required|max:255|min:5|string',
            'details' => 'required|string|min:10'
        ]);

        if($validate->fails())
        {
            return response()->json([
                'status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try {
            $branch_id = (new AuthController)->getBranch();

            $notice->update([
                'branch_id' => $branch_id,
                'notice_type_id' => $request->notice_type_id,
                'title' => $request->title,
                'details' => $request->details,
            ]);

            return response()->json([
                'status' => true,
                'data' => $notice
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
     *      path="/api/notice/{id}",
     *      operationId="deleteNotice",
     *      tags={"notice"},
     *      summary="Delete a notice",
     *      description="Delete a notice by its ID.",
     *      @OA\Parameter(
     *          name="id",
     *          description="ID of the notice",
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
     *      )
     * )
     */


    public function delete($id)
    {
        $notice = Notice::findOrFail($id);
        try
        {
            $notice->delete();

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
