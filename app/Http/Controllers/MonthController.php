<?php

namespace App\Http\Controllers;

use App\Models\Month;
use Illuminate\Http\Request;

class MonthController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/month",
     *     summary="Get all month",
     *     description="All month fetched",
     *     tags={"month"},
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
     *                     @OA\Property( property="name", type="string", example="April"),
     *             ))
     *         )
     *     ),
     * )
     */


    public function index()
    {
        $month = Month::orderBy('id')->get();

        return response()->json([
            'status' => true,
            'data' => $month], 200);
    }
}
