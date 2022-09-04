<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\GetCommentRequest;
use App\Models\Comment;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class CommentController extends Controller
{
    /**
     * This function is used to add comments to a forum.
     *
     * @param CreateCommentRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function create(CreateCommentRequest $request)
    {
        $responseCode = Response::HTTP_OK;
        $response = null;

        try {
            $request->validated();
            $user = User::findOrFail($request->user()->id);

            $forumModel = new Comment();
            $forumModel->forum_id = $request->forum_id;
            $forumModel->comment = $request->comment;
            $forumModel->comment_by = $user->id;

            $forumModel->save();

            $response = $forumModel;

            Log::channel('default_log')->info('Comment added', [
                'added_user_id' => $user->id,
                'forum_id' => $forumModel->forum_id
            ]);
        } catch (MethodNotAllowedHttpException $e) {
            $response = 'Wrong method call used';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Wrong method call used', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Comment: Create',
            ]);
        } catch (ModelNotFoundException $e) {
            $response = 'Invalid User Id provided. Something went wrong with the token. Please logout and login again.';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Invalid user id provided', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Comment: Create',
            ]);
        } catch (\Exception $e) {
            $response = [
                'message' => 'General exception received',
                'exception' => $e->getMessage(),
            ];

            $responseCode = $e->getCode();

            Log::channel('default_log')->error('General exception', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Comment: Create',
            ]);
        }

        return $request->sendJsonResponse($response, $responseCode);
    }

    /**
     * This function is used to get comments for a certain forum.
     *
     * @param GetCommentRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function get(GetCommentRequest $request)
    {
        $responseCode = Response::HTTP_OK;
        $response = null;

        try {

            $request->validated();
            $forum = Forum::findOrFail($request->forum_id);

            $comment = new Comment();
            $response = $comment->getComments($forum->id);

            Log::channel('default_log')->info('Comments viewed', [
                'viewed_user_id' => $request->user()->id,
                'forum_id' => $forum->id
            ]);
        } catch (MethodNotAllowedHttpException $e) {
            $response = 'Wrong method call used';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Wrong method call used', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Comment: Get',
            ]);
        } catch (ModelNotFoundException $e) {
            $response = 'Invalid Forum Id provided.';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Invalid forum id provided', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Comment: Get',
            ]);
        } catch (\Exception $e) {
            $response = [
                'message' => 'General exception received',
                'exception' => $e->getMessage(),
            ];

            $responseCode = $e->getCode();

            Log::channel('default_log')->error('General exception', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Comment: Get',
            ]);
        }

        return $request->sendJsonResponse($response, $responseCode);
    }
}
