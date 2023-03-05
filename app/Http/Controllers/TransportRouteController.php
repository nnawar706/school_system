<?php

namespace App\Http\Controllers;

use App\Models\TransportRoute;
use Illuminate\Http\Request;

class TransportRouteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/transport_route",
     *     summary="Get all transport route",
     *     tags={"transport route"},
     *
     *     @OA\Response(
     *         response="200",
     *         description="List of all transport route",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Main Branch"),
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
        if(TransportRoute::count() == 0)
        {
            return response()->json([], 204);
        }
        $transport_route = TransportRoute::latest()->get();

        return response()->json([
            'status' => true,
            'data' => $transport_route
        ], 200);
    }


}
