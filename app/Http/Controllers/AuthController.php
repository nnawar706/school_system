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
     *      path="/api/register",
     *      summary="Sign up",
     *      description="Sign up by branch_id, role_id, email, password",
     *      operationId="userRegister",
     *      tags={"auth"},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"branch_id","role_id","email","password"},
     *              @OA\Property(property="branch_id", type="integer", example=1),
     *              @OA\Property(property="role_id", type="integer", example=1),
     *              @OA\Property(property="email", type="string", format="email", example="user1@gmail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Authentication Failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false)
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Authorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                          @OA\Property(property="branch_id", type="integer", example=1),
     *                          @OA\Property(property="role_id", type="integer", example=1),
     *                          @OA\Property(property="email", type="string", format="email", example="user1@gmail.com"),
     *                          @OA\Property(property="id", type="integer", example=1),
     *              )
     *          ),
     *      ),
     * )
     */

    public function register(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'role_id' => 'required',
            'branch_id' => 'required',
            'registration_id' => 'required|unique:users',
            'password' => 'required|min:8|max:20'
        ]);

        if($validate->fails())
        {
            return response()->json(['status' => false,
                'error' => $this->showErrors($validate->errors())], 422);
        }
        try
        {
            $user = User::create([
                'role_id' => $request->role_id,
                'branch_id' => $request->branch_id,
                'registration_id' => $request->registration_id,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'data' => $user
            ], 201);
        }
        catch (QueryException $ex)
        {
            return response()->json([
                'status' => $ex->getMessage()], 500);
        }
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      summary="Sign in",
     *      description="Login by email, password",
     *      operationId="userLogin",
     *      tags={"auth"},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="registration_id", type="string", example="54763212"),
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Authentication Failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false)
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Authorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                          @OA\Property(property="access_token", type="string", example="blahblah"),
     *                          @OA\Property(property="token_type", type="string", example="bearer"),
     *                          @OA\Property(property="expires_in", type="integer", example=3600),
     *              )
     *          ),
     *      ),
     * )
     */


    public function login(Request $request)
    {
        $credentials = $request->only('registration_id', 'password');
        if ($token = $this->guard()->attempt($credentials))
        {
            User::where('registration_id', $request->registration_id)->update([
                'last_login_at' => date('Y-m-d H:i:s'),
                'last_login_ip' => $request->ip()
            ]);

            return $this->respondWithToken($token);
        }
        return response()->json(['status' => false], 401);
    }

    /**
     * @OA\Get(
     *     path="/api/me",
     *     summary="Logged in user",
     *     description="Logged in user profile data",
     *     tags={"auth"},
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
     *              @OA\Property(property="data", type="object",
     *                          @OA\Property(property="branch_id", type="integer", example=1),
     *                          @OA\Property(property="role_id", type="integer", example=1),
     *                          @OA\Property(property="email", type="string", format="email", example="user1@gmail.com"),
     *                          @OA\Property(property="id", type="integer", example=1),
     *              )
     *         )
     *     ),
     * )
     */

    public function me()
    {
        return response()->json([
            'status' => true,
            'data' => $this->guard()->user()]);
    }


    /**
     * @OA\Get(
     *     path="/api/logout",
     *     summary="Logging out",
     *     description="User log out",
     *     tags={"auth"},
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
     *         )
     *     ),
     * )
     */


    public function logout()
    {
        $this->guard()->logout();
        return response()->json(['status' => true]);
    }


    /**
     * @OA\Post(
     *      path="/api/refresh",
     *      summary="Refresh token",
     *      description="Refresh token for authorization",
     *      operationId="userRefresh",
     *      tags={"auth"},
     *
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"email","password"},
     *              @OA\Property(property="email", type="string", format="email", example="user1@gmail.com"),
     *              @OA\Property(property="password", type="string", format="password", example="PassWord12345"),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Authentication Failed",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false)
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Authorized",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                          @OA\Property(property="access_token", type="string", example="blahblah"),
     *                          @OA\Property(property="token_type", type="string", example="bearer"),
     *                          @OA\Property(property="expires_in", type="integer", example=3600),
     *              )
     *          ),
     *      ),
     * )
     */


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

    public function getBranch()
    {
//        $result = $this->me();
//        return $result->original['data']['branch_id'];
        return 1;
    }
}
