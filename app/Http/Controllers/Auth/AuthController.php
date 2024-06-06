<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\Auth\AuthException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * @throws AuthException
     */
    public function signUp(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ]);
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            return response()->json(new UserResource($user));
        } catch (Exception $e) {
            Log::error($e);
            throw new AuthException("Sorry, something went wrong");
        }
    }

    /**
     * @throws AuthException
     */
    public function signIn(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials'], 401);
            }
            $user->revokeTokens();
            $token = $user->createToken('SignIn - ' . now())->accessToken;

            return response()->json(['token' => $token]);
        } catch (Exception $e) {
            throw new AuthException("Sorry, something went wrong");
        }
    }
}
