<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Http\Request;



class RegisterController extends Controller
{
    
    public function register(Request $request)
    {
        if(!$request->name || !$request->email || !$request->password){
            return response()->json([
                "success"=> false,
                "msg"=> "Veuillez remplir tous les champs"
            ], 400);
        }
        if(strlen($request->password) < 8){
            return response()->json([
                "success"=> false,
                "msg"=> "Votre mot de passe doit contenir au moins 8 caractères"
            ], 400);
        }
        $exists = User::where('email', $request->email)->exists();

        if ($exists) {
            return response()->json(["msg" => "Vous aves déjà un compte"], 409);
        }
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'name' => $request->name
        ]);


        $token = $user->createToken($request->device_name)->plainTextToken;

        return response()->json([
            "token" => $token,
            "name" => $user->name,
            "email" => $user->email,
            "created_at" => $user->created_at
        ], 200);
        /**
     * @OA\Post(path="/api/auth/register",
     *   tags={"auth"},
     *   summary="Register user",
     *   description="Register a user",
     *   operationId="registerUser",
     * @OA\RequestBody(
    *    required=true,
    *    description="User email, name, password, device_name for register",
    *    @OA\JsonContent(
    *       required={"email", "name", "password"},
    *       @OA\Property(property="email", type="string", format="email", ref="#/components/schemas/User/properties/email"),
    *       @OA\Property(property="name", type="string", ref="#/components/schemas/User/properties/name"),
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
    *       @OA\Property(property="email", type="string", ref="#/components/schemas/User/properties/email"),
    *       @OA\Property(property="created_at", type="date-time", ref="#/components/schemas/User/properties/created_at"),
    *       
    
    *        )
    *     ),
    *   @OA\Response(
    *    response=400,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Veuillez remplir tous les champs ou Votre mot de passe doit contenir au moins 8 caractères"),
    *        )
    *     ),
    *   @OA\Response(
    *    response=409,
    *    description="error",
    *    @OA\JsonContent(
    *       @OA\Property(property="msg", type="string", example="Vous aves déjà un compte"),
    *        )
    *     ),
     * )
     */
    }
}
