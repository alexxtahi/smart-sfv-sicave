<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;

class AuthController extends Controller
{
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'login' => 'required',
                'password' => 'required',
            ]);

            $credentials = request(['login', 'password']);
            if (!Auth::attempt($credentials))
                return response()->json([
                    'message' => "Vous avez fourni des mauvais identifiants"
                ], 401);

            $user = $request->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();

            $user->update([
                'etat_user' => 1
            ]);

            return response()->json([
                'access_token' => 'Bearer ' . $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'full_name' => $user->full_name,
                'role' => $user->role,
                'email' => $user->email,
                'id' => $user->id,
                'contact' => $user->contact,
                'code' => 1,
                'message' => 'Connexion établie avec succès !',
                'expires_at' => Carbon::parse(
                    $tokenResult->token->expires_at
                )->toDateTimeString()
            ]);
        } catch (Exception $exc) {
            $jsonData["code"] = -1;
            $jsonData["data"] = null;
            $jsonData["msg"] = $exc->getMessage();
            return response()->json($jsonData);
        }
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        try {
            $user = $request->user();

            $user->update([
                'last_login_at' => Carbon::now()->toDateTimeString(),
                'last_login_ip' => $request->getClientIp(),
                'etat_user' => 0
            ]);
            $request->user()->token()->revoke();
            return response()->json([
                'message' => 'Deconnecté',
                'code' => 1,
            ]);
        } catch (Exception $exc) {
            $jsonData["code"] = -1;
            $jsonData["data"] = null;
            $jsonData["msg"] = $exc->getMessage();
            return response()->json($jsonData);
        }
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
