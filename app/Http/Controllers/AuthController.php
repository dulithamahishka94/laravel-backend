<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetTokenRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class AuthController extends Controller
{
    /**
     * This function is used to registered users to logged in into the system. The access token will be provided on successful
     * logins.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function login(LoginRequest $request)
    {
        $responseCode = Response::HTTP_OK;
        $response = null;

        try {
            $request->validated();

            $user = User::where('email', $request->email)->firstOrFail();

            if ($user) {
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                    $response = ['status' => true, 'user' => $user, 'token' => $token, 'isAdmin' => $user->isAdmin()];

                    Log::channel('default_log')->info('Token created and logged-in', [
                        'user_id' => $user->id,
                        'admin_status' => $user->isAdmin()
                    ]);
                } else {
                    $response = ["message" => "Password mismatch"];
                    $responseCode = Response::HTTP_UNPROCESSABLE_ENTITY;

                    Log::channel('default_log')->info('Password mismatch for user', [
                        'user_id' => $user->id,
                        'admin_status' => $user->isAdmin()
                    ]);
                }
            } else {
                $response = ["message" => 'User does not exist'];
                $responseCode = Response::HTTP_UNPROCESSABLE_ENTITY;

                Log::channel('default_log')->info('User does not exists', [
                    'email' => $request->email,
                ]);
            }
        } catch (MethodNotAllowedHttpException $e) {
            $response = 'Wrong method call used';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Wrong method call used', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Authentication: Login',
            ]);
        } catch (ModelNotFoundException $e) {
            $response = 'Invalid username provided';
            $responseCode = Response::HTTP_UNAUTHORIZED;

            Log::channel('default_log')->error('Invalid username provided', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Authentication: Login',
            ]);
        } catch (\Exception $e) {
            $response = [
                'message' => 'General exception received',
                'exception' => $e->getMessage(),
            ];

            $responseCode = $e->getCode();

            Log::channel('default_log')->error('General Exception', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Authentication: Login',
            ]);
        }

        return $request->sendJsonResponse($response, $responseCode);

    }

    /**
     * This function is used to register user into the system as a default user.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $responseCode = Response::HTTP_OK;
        $response = null;

        try {
            $request->validated();

            $response = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
        } catch (MethodNotAllowedHttpException $e) {
            $response = 'Wrong method call used';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Wrong method call used', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Authentication: Register',
            ]);
        } catch (\Exception $e) {
            $response = [
                'message' => 'General exception received',
                'exception' => $e->getMessage(),
            ];

            $responseCode = $e->getCode();

            Log::channel('default_log')->error('General Exception', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Authentication: Register',
            ]);
        }

        return $request->sendJsonResponse($response, $responseCode);
    }

    /**
     * This function is used to logout the user from the system. Token will be invalidated after.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logOut(Request $request)
    {
        auth()->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        Log::channel('default_log')->info('User Logged Out', [
            'user_id' => auth()->user()->id,
        ]);

        return response()->json('Logged out successfully', Response::HTTP_OK);
    }
}
