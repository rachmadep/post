<?php

namespace App\Http\Controllers\Post;

use App\Comment;
use App\Post;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PostController;

/**
 * CommentController
 */
class CommentController extends Controller
{
    /**
     * Relations
     * @param  \Illuminate\Http\Request|null $request
     * @param Post $post
     * @param Comment $comment
     * @return array
     */
    public static function relations(Request $request = null, Post $post = null, Comment $comment = null)
    {
        return [
            'post' => PostController::relations($request, $post)['post'],
            'comment' => [
                'belongsToMany' => [], // also for morphToMany
                'hasMany' => [], // also for morphMany, hasManyThrough
                'hasOne' => [], // also for morphOne
            ]
        ];
    }

    /**
     * Visibles
     * @param  \Illuminate\Http\Request|null $request
     * @param Post $post
     * @param Comment $comment
     * @return array
     */
    public static function visibles(Request $request = null, Post $post = null, Comment $comment = null)
    {
        return [
            'parent' => [
                'post' => PostController::visibles($request, $post)['show']['post']
            ],
            'index' => [
                'comment' => [
                    [ 'name' => 'comment', 'label' => ucwords(__('comments.comment')) ],
                    [ 'name' => 'user', 'label' => ucwords(__('comments.user')), 'column'=>'name' ],
                ]
            ],
            'show' => [
                'comment' => [
                    [ 'name' => 'comment', 'label' => ucwords(__('comments.comment')) ],
                ]
            ]
        ];
    }

    /**
     * Fields
     * @param  \Illuminate\Http\Request|null $request
     * @param Post $post
     * @param Comment $comment
     * @return array
     */
    public static function fields(Request $request = null, Post $post = null, Comment $comment = null)
    {
        return [
            'create' => [
                'comment' => [
                    [ 'field' => 'input', 'type' => 'text', 'name' => 'comment', 'label' => ucwords(__('comments.comment')), 'required' => true ],
                ]
            ],
            'edit' => [
                'comment' => [
                    [ 'field' => 'input', 'type' => 'text', 'name' => 'comment', 'label' => ucwords(__('comments.comment')) ],
                ]
            ]
        ];
    }

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
                'comment' => 'required|string|max:255',
            ],
            'update' => [
                'comment' => 'string|max:255',
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
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Post $post)
    {
        $this->authorize('view', $post);
        $comments = Comment::filter()
            ->where($post->getForeignKey(), $post->getKey())
            ->paginate()->appends(request()->query());
        $this->authorize('index', 'App\Comment');

        return response()->view('posts.comments.index', [
            'post' => $post,
            'comments' => $comments,
            'relations' => self::relations(request(), $post),
            'visibles' => array_merge(self::visibles(request(), $post)['parent'], self::visibles(request(), $post)['index']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Post $post)
    {
        $this->authorize('update', $post);
        $this->authorize('create', 'App\Comment');

        return response()->view('posts.comments.create', [
            'post' => $post,
            'relations' => self::relations(request(), $post),
            'visibles' => self::visibles(request(), $post)['parent'],
            'fields' => self::fields(request(), $post)['create']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Post $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, Post $post)
    {
        $this->authorize('update', $post);
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
        $comment->user_id = Auth::user()->id;
        $comment->save();

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('posts.comments.show', [ $post->getKey(), $comment->getKey() ]);

        return $response->withInput([
            $post->getForeignKey() => $post->getKey(),
            $comment->getForeignKey() => $comment->getKey(),
        ])->with('status', __('Success'));
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Post $post, Comment $comment)
    {
        $post->comments()->findOrFail($comment->getKey());
        $this->authorize('view', $post);
        $this->authorize('view', $comment);

        return response()->view('posts.comments.show', [
            'post' => $post,
            'comment' => $comment,
            'relations' => self::relations(request(), $post, $comment),
            'visibles' => array_merge(self::visibles(request(), $post, $comment)['parent'], self::visibles(request(), $post, $comment)['show'])
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Post $post, Comment $comment)
    {
        $post->comments()->findOrFail($comment->getKey());
        $this->authorize('update', $post);
        $this->authorize('update', $comment);

        return response()->view('posts.comments.edit', [
            'post' => $post,
            'comment' => $comment,
            'relations' => self::relations(request(), $post, $comment),
            'visibles' => self::visibles(request(), $post, $comment)['parent'],
            'fields' => self::fields(request(), $post, $comment)['edit']
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Post $post
     * @param Comment $comment
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Post $post, Comment $comment)
    {
        $post->comments()->findOrFail($comment->getKey());

        $this->authorize('update', $post);
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

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('posts.comments.show', [ $post->getKey(), $comment->getKey() ]);

        return $response->withInput([
            $post->getForeignKey() => $post->getKey(),
            $comment->getForeignKey() => $comment->getKey(),
        ])->with('status', __('Success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Post  $post
     * @param  \App\Comment  $comment
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Post $post, Comment $comment)
    {
        $post->comments()->findOrFail($comment->getKey());
        $this->authorize('update', $post);
        $this->authorize('delete', $comment);
        $comment->delete();

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()) && !str_contains(request()->redirect, '/'.array_last(explode('.', 'posts.comments')).'/'.$comment->getKey()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('posts.comments.index', $post->getKey());

        return $response->with('status', __('Success'));
    }
}
