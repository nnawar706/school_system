<?php

namespace App\Http\Controllers;

use App\Models\Religion;
use Illuminate\Http\Request;

class ReligionController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/religion",
     *     summary="Get all religion",
     *     description="All religion fetched",
     *     tags={"religion"},
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
     *                     @OA\Property( property="name", type="string", example="Islam"),
     *             ))
     *         )
     *     ),
     * )
     */


    public function index()
    {
        $religion = Religion::orderBy('id', 'desc')->get();

        return response()->json([
            'status' => true,
            'data' => $religion], 200);
    }
}
