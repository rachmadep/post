<?php

namespace App\Http\Controllers\Api\Post;

use Auth;
use App\Comment;
use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\Resource;

/**
 * CommentController
 * @extends Controller
 */
class CommentController extends Controller
{
    /**
     * Rules
     * @param  \Illuminate\Http\Request|null $request
     * @param Post $post
     * @param Comment $comment
     * @return array
     */
    public static function rules(Request $request = null, Post $post = null, Comment $comment = null)
    {
        return [
            'store' => [
                'comment' => 'required|string',
            ],
            'update' => [
                'comment' => 'string',
            ]
        ];
    }


    /**
    * Instantiate a new controller instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Post $post
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Post $post)
    {
        $this->authorize('view', $post);
        $comments = Comment::filter()
            ->where($post->getForeignKey(), $post->getKey())
            ->paginate()->appends(request()->query());
        $this->authorize('index', 'App\Comment');

        return Resource::collection($comments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Post $post
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, Post $post)
    {
//        $this->authorize('update', $post);
        $this->authorize('create', 'App\Comment');
        $request->validate(self::rules($request, $post)['store']);

        $comment = new Comment;
        foreach (self::rules($request, $post)['store'] as $key => $value) {
            if (str_contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $comment->{$key} = $request->file($key)->store('comments');
                } elseif ($request->exists($key)) {
                    $comment->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $comment->{$key} = $request->{$key};
            }
        }
        $comment->post()->associate($post);
        $comment->save();

        return (new Resource($comment))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @param Comment $comment
     * @return Resource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Post $post, Comment $comment)
    {
        $post->comments()->findOrFail($comment->getKey());
        $this->authorize('view', $post);
        $this->authorize('view', $comment);

        return new Resource($comment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Post $post
     * @param Comment $comment
     * @return Resource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Post $post, Comment $comment)
    {
        $post->comments()->findOrFail($comment->getKey());

//        $this->authorize('update', $post);
        $this->authorize('update', $comment);
        $request->validate(self::rules($request, $post, $comment)['update']);

        foreach (self::rules($request, $post, $comment)['update'] as $key => $value) {
            if (str_contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $comment->{$key} = $request->file($key)->store('comments');
                } elseif ($request->exists($key)) {
                    $comment->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $comment->{$key} = $request->{$key};
            }
        }
        $comment->post()->associate($post);
        $comment->save();

        return new Resource($comment);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @param Comment $comment
     * @return Resource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Post $post, Comment $comment)
    {
        $post->comments()->findOrFail($comment->getKey());
//        $this->authorize('update', $post);
        $this->authorize('delete', $comment);
        $comment->delete();

        return new Resource($comment);
    }
}
