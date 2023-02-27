<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/notice",
     *     summary="Get all notice",
     *     description="All notice fetched",
     *     tags={"notice"},
     *
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
     *                 type="array", @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property( property="title", type="string", example="Something"),
     *                     @OA\Property( property="details", type="string", example="Details of Something"),
     *                     @OA\Property(property="created_at", type="string", example="2021-05-05 12:00:00"),
     *                     @OA\Property(property="updated_at", type="string", example="2021-05-05 12:00:00"),
     *             ))
     *         )
     *     ),
     *
     *     @OA\Response(
     *         response="204",
     *         description="Branch not found",
     *     )
     * )
     */


    public function index()
    {
        if(Notice::count() == 0)
        {
            return response()->json([], 204);
        }

        $notice = Notice::with(['notice_type' => function($query) {
            return $query->select('id', 'name');
        }])->latest()->get();

        return response()->json([
            'status' => true,
            'data' => $notice], 200);
    }
}
