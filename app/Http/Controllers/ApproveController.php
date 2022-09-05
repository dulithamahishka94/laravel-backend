<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveRequest;
use App\Models\Approve;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class ApproveController extends Controller
{
    /**
     * This function will be used to approve the forums created by a default user.
     *
     * @param ApproveRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function approve(ApproveRequest $request)
    {
        $responseCode = Response::HTTP_OK;
        $response = null;

        try {
            $request->validated();
            $user = User::findOrFail($request->user()->id);

            if (Gate::allows('approve-forum', $user)) {
                $forum = Forum::findOrFail($request->forum_id);

                $forum->approved = Forum::FORUM_APPROVED;
                $forum->approved_by = $user->id;

                $forum->save();
                $response = $forum;

                Log::channel('default_log')->info('Forum approved by user', [
                    'user_id' => $user->id,
                    'forum_user_id' => $forum->user_id,
                    'forum_id' => $forum->id
                ]);
            } else {
                $response = 'Unauthorized access to approve forums';
                $responseCode = Response::HTTP_UNAUTHORIZED;

                Log::channel('default_log')->error('Unauthorized access to approve forums', [
                    'user_id' => $request->user()->id,
                    'error_code' => $responseCode,
                    'function' => 'Approve: Reject',
                ]);
            }
        } catch (MethodNotAllowedHttpException $e) {
            $response = 'Wrong method call used';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Wrong method call used', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Forum: Approve',
            ]);
        } catch (ModelNotFoundException $e) {
            $response = 'Invalid forum id or user id provided';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Invalid forum id or user id provided', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Forum: Approve',
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
                'function' => 'Forum: Approve',
            ]);
        }

        return $request->sendJsonResponse($response, $responseCode);
    }

    /**
     * This function is to reject the forums that was created by a default user.
     *
     * @param ApproveRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function reject(ApproveRequest $request)
    {
        $responseCode = Response::HTTP_OK;
        $response = null;

        try {
            $request->validated();

            $user = User::findOrFail($request->user()->id);
            if (Gate::allows('reject-forum', $user)) { // Check whether the user has reject permissions.
                $forum = Forum::findOrFail($request->forum_id);

                $forum->approved = Forum::FORUM_REJECTED;
                $forum->approved_by = $user->id;
                $forum->save();

                $response = $forum;

                Log::channel('default_log')->info('Forum rejected by user', [
                    'user_id' => $user->id,
                    'forum_user_id' => $forum->user_id,
                    'forum_id' => $forum->id
                ]);
            } else {
                $response = 'Unauthorized access to reject forums';
                $responseCode = Response::HTTP_UNAUTHORIZED;

                Log::channel('default_log')->error('Unauthorized access to reject forums', [
                    'user_id' => $request->user()->id,
                    'error_code' => $responseCode,
                    'function' => 'Approve: Reject',
                ]);
            }
        } catch (MethodNotAllowedHttpException $e) {
            $response = 'Wrong method call used';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Wrong method call used', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Approve: Reject',
            ]);
        } catch (ModelNotFoundException $e) {
            $response = 'Invalid forum id or user id provided.';
            $responseCode = $e->getCode();

            Log::channel('default_log')->error('Invalid forum id or user id provided', [
                'error_code' => $e->getCode(),
                'exception' => $e->getMessage(),
                'function' => 'Approve: Approve',
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
                'function' => 'Forum: Reject',
            ]);
        }

        return $request->sendJsonResponse($response, $responseCode);
    }
}
