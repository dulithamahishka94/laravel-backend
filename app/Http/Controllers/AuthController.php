<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function getToken(Request $request)
    {
        // This will not work on php artisan serve. Check the link https://stackoverflow.com/questions/44879574/laravel-server-hangs-whenever-i-try-to-request-localhost8000-any-using-guzzle
        $response = Http::asForm()->post(config('services.passport.login_endpoint'),
            [
                'grant_type' => 'password',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username' => $request->username,
                'password' => $request->password,
            ]
        );

        return $response->body();
    }

    public function login(LoginRequest $request)
    {
        $request->validated();

        $user = User::where('email', $request->email)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = ['status' => true, 'user' => $user, 'token' => $token, 'isAdmin' => User::find($user->id)->isAdmin()];

                return $request->sendJsonResponse($response, 200);
//                return response($response, 200);
            } else {
                $response = ["message" => "Password mismatch"];

                return $request->sendJsonResponse($response, 422);
            }
        } else {
            $response = ["message" => 'User does not exist'];
//            return response($response, 422);
            return $request->sendJsonResponse($response, 422);
        }

    }

    public function register(RegisterRequest $request)
    {
        $request->validated();

        return User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    }

    public function logOut(Request $request)
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json('Logged out successfully', 200);
    }
}
