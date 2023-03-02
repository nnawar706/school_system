<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/designation",
     *     summary="Get all designation",
     *     tags={"designation"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all designation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Associate Professor"),
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
        if(Designation::count() == 0)
        {
            return response()->json([], 204);
        }
        $designation = Designation::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $designation
        ], 200);
    }
}
