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
     * Pending status
     */
    const STATUS_PENDING = 0;

    /**
     * Forum approved status
     */
    const FORUM_APPROVED = 1;

    /**
     * Forum rejected status
     */
    const FORUM_REJECTED = 3;

    /**
     * Forum deleted status
     */
    const FORUM_DELETED = 1;

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

    // This function contains the relationship to the Comment::class.
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
