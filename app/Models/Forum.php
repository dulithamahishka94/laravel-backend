<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    protected $table = 'forums';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'approved',
        'approved_by',
        'deleted',
        'deleted_by',
        'user_id'
    ];

//    public function users()
//    {
//        return $this->belongsTo(User::class, 'user_id', 'id');
//    }
//
//    public function comments()
//    {
//        return $this->hasMany(Comment::class, 'forum_id', 'id');
//    }


}
