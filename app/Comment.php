<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

// Swagger Annotation for Comment-Schema
/**
 * @OA\Schema(
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="body", type="string"),
 *      @OA\Property(property="comment_level", type="integer"),
 *      @OA\Property(property="reply_id", type="integer")
 * )
 */
class Comment extends Model
{
    use SoftDeletes;

    protected $dates = ['created_at', 'updated_at'];

    /**
    * Fillable fields for a course
    *
    * @return array
    */
    protected $fillable = ['name', 'body', 'comment_level', 'reply_id'];

    public function replies()
    {
        return $this->hasMany('App\Comment','id','reply_id');
    }
}
