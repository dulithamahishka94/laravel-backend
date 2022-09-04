<?php

use App\Http\Controllers\ApproveController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ForumController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);

// Forum
Route::middleware('auth:api')->post('/create-forum', [ForumController::class, 'create']);
Route::middleware('auth:api')->post('/delete-forum', [ForumController::class, 'delete']);
Route::middleware('auth:api')->post('/list', [ForumController::class, 'index']);

// Comments
Route::middleware('auth:api')->post('/create-comment', [CommentController::class, 'create']);
Route::middleware('auth:api')->post('/view-comments', [CommentController::class, 'get']);

// Approvals
Route::middleware('auth:api')->post('/approve', [ApproveController::class, 'approve']);
Route::middleware('auth:api')->post('/reject', [ApproveController::class, 'reject']);

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact administrator'], Response::HTTP_NOT_FOUND);
});
