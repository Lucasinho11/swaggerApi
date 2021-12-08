<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if(!$request->email || !$request->password){
            return response()->json([
                "success"=> false,
                "msg"=> "Veuillez remplir tous les champs"
            ], 400);
        }
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                "msg" => "Identifiants incorrects"
            ], 401);
        }
        $user->tokens()->where('tokenable_id',  $user->id)->delete();

        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            "token" => $token,
            "name" => $user->name,
            "email" => $user->email
        ], 200);
        
    /**
     * @OA\Post(path="/api/auth/login",
     *   tags={"auth"},
     *   summary="Login user",
     *   description="Login a registered user",
     *   operationId="loginUser",
     * @OA\RequestBody(
    *    required=true,
    *    description="User email and password for login",
    *    @OA\JsonContent(
    *       required={"email","password"},
    *       @OA\Property(property="email", type="string", format="email", ref="#/components/schemas/User/properties/email"),
    *       @OA\Property(property="password", type="string", format="password", ref="#/components/schemas/User/properties/password"),
    *       @OA\Property(property="device_name", type="string", example="Swagger"),
    *    ),
    * ),
     *  @OA\Response(
    *    response=200,
    *    description="Success",
    *    @OA\JsonContent(
    *       @OA\Property(property="token", type="string"),
    *       @OA\Property(property="name", type="string", ref="#/components/schemas/User/properties/name"),
    *       @OA\Property(property="email", type="string", ref="#/components/schemas/User/properties/email")
    *       
    
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Veuillez remplir tous les champs"),
    *        )
    *     ),
    *   @OA\Response(
    *    response=401,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Identifiants incorrects"),
    *        )
    *     ),
     * )
     */
    }
    public function logout(Request $request)
    {

        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }
}
