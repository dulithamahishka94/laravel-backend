<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateForumRequest;
use App\Http\Requests\DeleteForumRequest;
use App\Http\Requests\ShowForumsRequest;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;


class ForumController extends Controller
{
    /**
     * This function is used to create forums. Default forums will need an approval.
     *
     * @param CreateForumRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function create(CreateForumRequest $request)
    {
        $responseCode = Response::HTTP_OK;
        $response = null;

        try {
            $request->validated();

            $user = User::findOrFail($request->user()->id);
            $forumModel = new Forum();
            $forumModel->title = $request->title;
            $forumModel->description = $request->description;

            $approved = Forum::STATUS_PENDING;
            $approvedBy = null;

            // Admins should be able to create forums without approvals.
            if (Gate::allows('post-without-approval', $user)) {
                $approved = Forum::FORUM_APPROVED;
                $approvedBy = $user->id;
            }

            $forumModel->approved = $approved;
            $forumModel->approved_by = $approvedBy;
            $forumModel->deleted = Forum::STATUS_PENDING;
            $forumModel->deleted_by = null;
            $forumModel->user_id = $user->id;

            $forumModel->save();

            $response = $forumModel;

            Log::channel('default_log')->info('Forum created', [
                'added_user_id' => $request->user()->id,
                'forum_id' => $forumModel->forum_id
            ]);
        } catch (MethodNotAllowedHttpException $e) {
            $response = 'Wrong method call used';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Wrong method call used', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Forum: Create',
            ]);
        } catch (ModelNotFoundException $e) {
            $response = 'Invalid user id provided. Something went wrong with the token. Please logout and login again.';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Invalid user id provided', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Forum: Create',
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
                'function' => 'Forum: Create',
            ]);
        }

        return $request->sendJsonResponse($response, $responseCode);
    }

    /**
     * This function is used to delete the forums for the user.
     *
     * @param DeleteForumRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function delete(DeleteForumRequest $request)
    {
        $responseCode = Response::HTTP_OK;
        $response = null;

        try {
            $request->validated();

            $forumId = $request->forum_id;
            $forum = Forum::findOrFail($forumId);
            $user = User::findOrFail($request->user()->id);

            $forum->deleted = Forum::FORUM_DELETED; // add const
            $forum->deleted_by = $user->id;
            $forum->save();

            $response = $forum;

            // Delete comments related to the forum.
            $forum->comments()->delete();

            Log::channel('default_log')->info('Forum deleted', [
                'added_user_id' => $request->user()->id,
                'forum_id' => $forumId
            ]);
        } catch (MethodNotAllowedHttpException $e) {
            $response = 'Wrong method call used';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Wrong method call used', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Forum: Delete',
            ]);
        } catch (ModelNotFoundException $e) {
            $response = 'Invalid forum id or user id provided';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Invalid forum id or user id provided', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Forum: Delete',
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
                'function' => 'Forum: Delete',
            ]);
        }

        return $request->sendJsonResponse($response, $responseCode);
    }

    /**
     * This function is used to get all the forums in the listing page.
     *
     * @param ShowForumsRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function index(ShowForumsRequest $request)
    {
        $responseCode = Response::HTTP_OK;
        $response = null;

        try {
            $request->validated();
            $user = User::findOrFail($request->user()->id);

            $forums = Forum::where('deleted', Forum::STATUS_PENDING);

            // Default users can only see the forums that were already approved.
            if (!$user->isAdmin()) {
                $forums->where('approved', Forum::FORUM_APPROVED);
            } else {
                $forums->where('approved', '<>', Forum::FORUM_REJECTED);
            }

            // If search string exists.
            if (!empty($request->search)) {
                $forums->where('title', 'like', '%' . $request->search . '%');
            }

            $response = $forums->get();
        } catch (MethodNotAllowedHttpException $e) {
            $response = 'Wrong method call used';
            $responseCode = $e->getCode();
        } catch (ModelNotFoundException $e) {
            $response = 'Invalid User Id provided. Something went wrong with the token. Please logout and login again.';
            $responseCode = $e->getCode();
        } catch (\Exception $e) {
            $response = [
                'message' => 'General exception received',
                'exception' => $e->getMessage(),
            ];

            $responseCode = $e->getCode();

            Log::channel('default_log')->error('General exception', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Forum: Index',
            ]);
        }

        return $request->sendJsonResponse($response, $responseCode);
    }
}
