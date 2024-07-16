<?php

namespace App\Http\Controllers;

use App\Helper\ApiResponse;
use App\Models\user;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function logout(Request $req): JsonResponse
    {
        $req->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }

    public function login(Request $req): JsonResponse
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error('Validation Error', $validator->errors(), 422);
        }
        $credentials = $req->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = User::where('id', Auth::user()->id)->first();
            $token = $token = $user->createToken('user-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'login successfully',
                'data' => $user,
                'token' => $token,
            ], 200);
        } else {
            return ApiResponse::error('Unauthorized email or password wrong', [], 401);
        }

    }

    public function register(Request $req): JsonResponse
    {
        $validator = Validator::make($req->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|string|min:8',
        ]);
        if ($validator->fails()) {
            return ApiResponse::error('Validation Error', $validator->errors());
        }
        $user = new User([
            'name' => $req->get('name'),
            'email' => $req->get('email'),
            'password' => $req->get('password'),
        ]);
        if ($user->save()) {
            return ApiResponse::success($user, 'User registered successfully', 200);
        } else {
            return ApiResponse::error('Error When Register A user');
        }
    }

    public function edit(Request $request): JsonResponse
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|min:3',
            'email' => 'nullable|exists:users,email',
            'password' => 'nullable|string|min:5',
        ]);

        if ($validator->fails()) {
            return ApiResponse::error('Validation Error', $validator->errors());
        }

        try {
            if ($request->filled('name')) {
                $user->name = $request->name;
            }

            if ($request->filled('email')) {
                $user->email = $request->email;
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return ApiResponse::success($user, 'User updated successfully', 200);
        } catch (Exception $e) {
            return ApiResponse::error($e->getMessage());
        }
    }
}
