<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use Illuminate\Http\Request;

class GenderController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/gender",
     *     summary="Get all gender",
     *     description="All gender fetched",
     *     tags={"gender"},
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
     *                     @OA\Property( property="name", type="string", example="Female"),
     *             ))
     *         )
     *     ),
     * )
     */


    public function index()
    {
        $gender = Gender::orderBy('id')->get();

        return response()->json([
            'status' => true,
            'data' => $gender
        ], 200);
    }
}
