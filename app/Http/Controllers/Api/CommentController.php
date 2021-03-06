<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;

use App\Comment;

// Swagger Annotation for Comment-Service
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Comment Api",
 *      description="This is the Comment API documentation.",
 * )
 * @OA\Server(
 *      url="http://api.comment.com/v1",
 *      description="Local Server"
 * )
 * @OA\Tag(
 *     name="comments-service",
 *     description="Comment Controller",
 * )
 * @OA\Post(
 *      path="/comments",
 *      operationId="store",
 *      tags={"comments-service"},
 *      summary="Add comment to the static post",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(
 *                     property="name",
 *                     type="string",
 *                 ),
 *                 @OA\Property(
 *                     property="body",
 *                     type="string"
 *                 ),
 *                 @OA\Property(
 *                     property="comment_level",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="reply_id",
 *                     type="integer"
 *                 )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized",
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Unprocessable Entity",
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal Server",
 *      ),
 * )
 * @OA\Get(
 *      path="/comments",
 *      operationId="index",
 *      tags={"comments-service"},
 *      summary="Get all comments",
 *      @OA\Response(
 *          response=200,
 *          description="Success",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized",
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal Server",
 *      )
 * )
 */
class CommentController extends Controller
{
    const SUCCESS_STATUS = 200;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;
    const INTERNAL_SERVER_STATUS = 500;

    use ValidatesRequests;

    /**
     * Store a newly created comment in storage
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'body' => 'required',
            'comment_level' => 'filled',
            'reply_id' => 'filled'
        ]);

        $comment = Comment::create($request->all());

        if ($comment) {
            return response()->json([
                "status" => true,
                "comment" => $comment
            ], self::SUCCESS_STATUS);
        }
    }

    /**
     * Get all comments in storage
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request) {
        $comments = Comment::where('comment_level', 1)->get();

        $commentsData = [];

        foreach($comments as $key) {
            $replies = $this->replies($key->id);

            array_push($commentsData, [
                "name" => $key->name,
                "id" => $key->id,
                "body" => $key->body,
                "comment_level" => $key->comment_level,
                "replies" => $replies,
                "date" => $key->created_at->toDateTimeString()
            ]);
        }

        $collection = collect($commentsData);
        $collection->sortBy('date');

        if ($collection) {
            return response()->json([
                "status" => true,
                "comments" => $collection
            ], self::SUCCESS_STATUS);
        }
    }

    /**
     * Get all replies in a comment
     */
    protected function replies($commentId) {
        $comments = Comment::where('reply_id', $commentId)->get();

        $replies = [];
        foreach($comments as $comment) {
            $secondLvlReplies = Comment::where('reply_id', $comment->id)->get();

            array_push($replies, [
                "name" => $comment->name,
                "id" => $comment->id,
                "body" => $comment->body,
                "comment_level" => $comment->comment_level,
                "replies" => $secondLvlReplies,
                "date" => $comment->created_at->toDateTimeString()
            ]);
        }

        $collection = collect($replies);

        return $collection->sortBy('date');
    }
}
