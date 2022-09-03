<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    const DEFAULT_USER = 1;
    const ADMIN = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAdmin() : bool
    {
        return $this->type === self::ADMIN;
    }

    public function comments()
    {
         return $this->hasManyThrough(Comment::class, Forum::class, 'user_id','comment_by','id','id');
    }
//    /**
//     * Get the post that owns the comment.
//     */
//    public function comments()
//    {
//        return $this->belongsTo(Comment::class, 'comment_by', 'id');
//    }

//    public function comments()
//    {
//        return $this->hasManyThrough(
//            Forum::class,
//            Comment::class,
//            'forum_id',
//            'comment_by',
//            'id',
//            'id'
//        );
//    }
}
