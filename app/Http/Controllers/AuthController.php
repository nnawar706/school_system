<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Sign up",
     *     description="Signup by role_id, branch_id, email, password",
     *     operationId="userRegister",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *          required=true,
     *          description="Pass user credentials",
     *          @OA\JsonContent(
     *              required={"role_id", "branch_id", "email","password"},
     *              @OA\Property(property="role_id", type="integer", format="role_id", example=1),
     *              @OA\Property(property="branch_id", type="integer", format="role_id", example=1),
     *              @OA\Property(property="email", type="string", format="email", example="user1@gmail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *          ),
     *     ),
     *     @OA\Response(
     *          response=401,
     *          description="Authentication Failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false)
     *          ),
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Authorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true)
     *          ),
     *     ),
     * )
     */

    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'role_id' => 'required',
            'branch_id' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|max:20'
        ]);
        if($validate->fails()) {
            return response()->json($validate->errors(), 422);
        }
        try {
            $user = User::create([
                'role_id' => $request->role_id,
                'branch_id' => $request->branch_id,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);
            return response()->json([
                'message' => 'User registered successfully',
                'data' => $user
            ], 201);
        } catch (QueryException $ex) {
            $message = ($ex->getCode() == 23000) ? 'Invalid input data' : 'Database error';
            return response()->json(['message' => $message], 500);
        }
    }

    /**
     * @OA\Post(
     * path="/login",
     * summary="Sign in",
     * description="Login by email, password",
     * operationId="userLogin",
     * tags={"Auth"},
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="user1@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *    ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Authentication Failed",
     *    @OA\JsonContent(
     *        @OA\Property(property="status", type="boolean", example=false)
     *    ),
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Authorized",
     *    @OA\JsonContent(
     *        @OA\Property(property="status", type="boolean", example=true)
     *    ),
     * ),
     * )
     */

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if ($token = $this->guard()->attempt($credentials)) {
            return $this->respondWithToken($token);
        }
        return response()->json(['status' => false], 401);
    }

    public function me()
    {
        return response()->json([
            'status' => true,
            'data' => $this->guard()->user()]);
    }

    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['status' => true]);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        $data = [
        'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ];
        return response()->json([
            'status' => true,
            'data' => $data
        ], 200);
    }

    public function guard()
    {
        return Auth::guard();
    }
}
