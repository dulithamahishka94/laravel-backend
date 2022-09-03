<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'forum_comments';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'comment',
    ];

    /**
     * Get the post that owns the comment.
     */
    public function forum()
    {
        return $this->belongsTo(Forum::class, 'forum_id', 'id');
    }

    public function users()
    {
        return $this->hasOne(User::class, 'comment_by', 'id');
    }

    public function getComments($forumId)
    {
        $users = DB::table('forums')
            ->join('forum_comments', 'forum_comments.forum_id', '=', 'forums.id')
            ->join('users', 'users.id', '=', 'forum_comments.comment_by')
             ->where('forums.id', '=', $forumId)
            ->select('forum_comments.comment','users.name', 'users.id', 'forums.id')
            ->get();

        return $users;
    }
}
