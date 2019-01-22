<?php

namespace App\Http\Controllers\Api\User;

use App\Post;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\Resource;

/**
 * PostController
 * @extends Controller
 */
class PostController extends Controller
{
    /**
     * Rules
     * @param  \Illuminate\Http\Request|null $request
     * @param User $user
     * @param Post $post
     * @return array
     */
    public static function rules(Request $request = null, User $user = null, Post $post = null)
    {
        return [
            'store' => [
                'post' => 'required|string',
            ],
            'update' => [
                'post' => 'string',
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
     * @param User $user
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(User $user)
    {
        $this->authorize('view', $user);
        $posts = Post::filter()
            ->where($user->getForeignKey(), $user->getKey())
            ->paginate()->appends(request()->query());
        $this->authorize('index', 'App\Post');

        return Resource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request, User $user)
    {
//        $this->authorize('update', $user);
        $this->authorize('create', 'App\Post');
        $request->validate(self::rules($request, $user)['store']);

        $post = new Post;
        foreach (self::rules($request, $user)['store'] as $key => $value) {
            if (str_contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $post->{$key} = $request->file($key)->store('posts');
                } elseif ($request->exists($key)) {
                    $post->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $post->{$key} = $request->{$key};
            }
        }
        $post->user()->associate($user);
        $post->save();

        return (new Resource($post))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @param Post $post
     * @return Resource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(User $user, Post $post)
    {
        $user->posts()->findOrFail($post->getKey());
        $this->authorize('view', $user);
        $this->authorize('view', $post);

        return new Resource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @param Post $post
     * @return Resource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, User $user, Post $post)
    {
        $user->posts()->findOrFail($post->getKey());

//        $this->authorize('update', $user);
        $this->authorize('update', $post);
        $request->validate(self::rules($request, $user, $post)['update']);

        foreach (self::rules($request, $user, $post)['update'] as $key => $value) {
            if (str_contains($value, [ 'file', 'image', 'mimetypes', 'mimes' ])) {
                if ($request->hasFile($key)) {
                    $post->{$key} = $request->file($key)->store('posts');
                } elseif ($request->exists($key)) {
                    $post->{$key} = $request->{$key};
                }
            } elseif ($request->exists($key)) {
                $post->{$key} = $request->{$key};
            }
        }
        $post->user()->associate($user);
        $post->save();

        return new Resource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param User $user
     * @param Post $post
     * @return Resource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(User $user, Post $post)
    {
        $user->posts()->findOrFail($post->getKey());
//        $this->authorize('update', $user);
        $this->authorize('delete', $post);
        $post->delete();

        return new Resource($post);
    }
}
