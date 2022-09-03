<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApproveRequest;
use App\Models\Approve;
use App\Models\Forum;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    public function approve(ApproveRequest $request)
    {
        $forumId = $request->forum_id;

        $status = 0;
        $forum = Forum::find($forumId);

        if (!$request->status) {
            $forum->approved = 0;
            $forum->deleted = 1;
            $forum->deleted_by = $request->user()->id;
        } else {
            $forum->approved = 1;
            $forum->approved_by = $request->user()->id;
        }

        $forum->save();

        return $request->sendJsonResponse($forum, 200);
    }

    public function reject(ApproveRequest $request)
    {
        $forumId = $request->forum_id;

        $forum = Forum::find($forumId);

        $forum->approved = 3;
        $forum->approved_by = $request->user()->id;
        $forum->save();

        return $request->sendJsonResponse($forum, 200);
    }
}
