<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateForumRequest;
use App\Http\Requests\DeleteForumRequest;
use App\Http\Requests\ShowForumsRequest;
use App\Models\Forum;
use App\Models\ResponseJson;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class ForumController extends Controller
{
    public function create(CreateForumRequest $request)
    {
        $request->validated();
        $request->user()->id;

        $forumModel = new Forum();
        $forumModel->title = $request->title;
        $forumModel->description = $request->description;

        $approved = 0;
        $approvedBy = null;

        // Admins should be able to create forums without approvals.
        if (User::find($request->user()->id)->isAdmin()) {
            $approved = 1;
            $approvedBy = $request->user()->id;
        }

        $forumModel->approved = $approved;
        $forumModel->approved_by = $approvedBy;
        $forumModel->deleted = 0;
        $forumModel->deleted_by = null;
        $forumModel->user_id = $request->user()->id;

        $forumModel->save();

        return $request->sendJsonResponse($forumModel, 200, );
    }

    public function delete(DeleteForumRequest $request)
    {
        $forumId = $request->forum_id;

        $forum = Forum::find($forumId);

        $forum->deleted = 1;
        $forum->deleted_by = $request->user()->id;
        $forum->save();

        return $request->sendJsonResponse($forum, 200);
    }

    public function index(ShowForumsRequest $request)
    {
        $userId = $request->user()->id;
        $user = User::find($userId);

        $forums = Forum::where('deleted', 0);

        if (!$user->isAdmin()) {
            $conditions['approved'] = 1;
            $forums->where('approved', 1);
        }

        return $request->sendJsonResponse($forums->get(), 200);
    }
}
