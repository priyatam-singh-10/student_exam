<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator; 
use Illuminate\Validation\ValidationException;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

public function Register(Request $request)
{

    $validator =  Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:8',
        'role' => 'sometimes|in:student,admin',
    ]);

    if ($validator->fails()) {
        return response()->json([$validator->errors()], 422);
    }

    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->role = $request->role ?? 'student';
    $user->save();

    return response()->json([
            'success' => 'true',
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    
}

public function Login(Request $request)
{
    $credentials = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (!$token = auth('api')->attempt($credentials->validated())) {
        throw ValidationException::withMessages(['email' => 'Invalid credentials']);
    }
    return response()->json([
        'success' => 'true',
        'message' => 'Login successfully',
        'user' => auth('api')->user(),
        'token' => $token,
    ]);
}

public function MyProfile(Request $request)
{
    $user = Auth::guard('api')->user();
    return response()->json([
        'success' => 'true',
        'message' => 'User profile retrieved successfully',
        'user' => $user
    ]);
}

public function Logout()
{
    auth('api')->invalidate(true);
    return response()->json(['message' => 'Logged out']);
}

}

