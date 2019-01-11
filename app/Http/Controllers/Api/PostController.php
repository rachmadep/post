<?php

namespace App\Http\Controllers\Api;

use Auth;
use App\Post;
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
     * @param Post $post
     * @return array
     */
    public static function rules(Request $request = null, Post $post = null)
    {
        return [
            'store' => [
                'post' => 'required|string|max:255',
            ],
            'update' => [
                'post' => 'string|max:255',
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
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $posts = Post::filter()
            ->paginate()->appends(request()->query());
        $this->authorize('index', 'App\Post');

        return Resource::collection($posts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        $this->authorize('create', 'App\Post');
        $request->validate(self::rules($request)['store']);

        $post = new Post;
        foreach (self::rules($request)['store'] as $key => $value) {
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
        $post->save();

        return (new Resource($post))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return Resource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        return new Resource($post);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Post $post
     * @return Resource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);
        $request->validate(self::rules($request, $post)['update']);

        foreach (self::rules($request, $post)['update'] as $key => $value) {
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
        $post->save();

        return new Resource($post);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return Resource
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        return new Resource($post);
    }
}
