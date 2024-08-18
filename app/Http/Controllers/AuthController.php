<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string|min:6|same:password',
            'role' => 'nullable|string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole($request->role);

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            Log::channel('jwt')->error('Could not create token during registration', ['exception' => $e]);
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = Auth::attempt($credentials)) {
                throw new UnauthorizedHttpException('', 'Invalid credentials');
            }

            $user = Auth::user();

            return $this->respondWithToken($token, $user);
        } catch (TokenExpiredException $e) {
            Log::warning('Token has expired during login', ['exception' => $e]);
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            Log::error('Token is invalid during login', ['exception' => $e]);
            return response()->json(['error' => 'Token is invalid'], 401);
        } catch (JWTException $e) {
            Log::error('Could not create token during login', ['exception' => $e]);
            return response()->json(['error' => 'Could not create token'], 500);
        } catch (UnauthorizedHttpException $e) {
            Log::warning('Invalid credentials during login', ['exception' => $e]);
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function logout()
    {
        try {
            Auth::logout();
        } catch (JWTException $e) {
            Log::error('Could not invalidate token during logout', ['exception' => $e]);
            return response()->json(['error' => 'Could not log out'], 500);
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => $user,
        ]);
    }
}
