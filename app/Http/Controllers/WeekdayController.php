<?php

namespace App\Http\Controllers;

use App\Models\Weekday;
use Illuminate\Http\Request;

class WeekdayController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/weekday",
     *     summary="Get all weekday",
     *     description="All weekday fetched",
     *     tags={"weekday"},
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
     *                     @OA\Property( property="name", type="string", example="Sunday"),
     *             ))
     *         )
     *     ),
     * )
     */


    public function index()
    {
        $day = Weekday::orderBy('id')->get();

        return response()->json([
            'status' => true,
            'data' => $day], 200);
    }
}
