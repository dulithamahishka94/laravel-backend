<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCommentRequest;
use App\Http\Requests\GetCommentRequest;
use App\Models\Comment;
use App\Models\Forum;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function create(CreateCommentRequest $request)
    {
        $request->validated();
        $request->user()->id;

        $forumModel = new Comment();
        $forumModel->forum_id = $request->forum_id;
        $forumModel->comment = $request->comment;
        $forumModel->comment_by = $request->user()->id;

        $forumModel->save();

        return $request->sendJsonResponse($forumModel, 200);
    }

    public function get(GetCommentRequest $request)
    {
        $request->validated();
        $forumid = $request->forum_id;

        $comment = new Comment();
        $comments = $comment->getComments($forumid);
//        dd($comments);
//        $comments = Forum::find($forumid)->comments()->users()->get();
//        $comments = User::find($forumid)->comments;
        return $request->sendJsonResponse($comments, 200);
    }
}
