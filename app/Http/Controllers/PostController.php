<?php

namespace App\Http\Controllers;

use App\Post;
use Auth;
use Illuminate\Http\Request;

/**
 * PostController
 */
class PostController extends Controller
{
    /**
     * Relations
     * @param  \Illuminate\Http\Request|null $request
     * @param Post $post
     * @return array
     */
    public static function relations(Request $request = null, Post $post = null)
    {
        return [
            'post' => [
                'belongsToMany' => [], // also for morphToMany
                'hasMany' => [
                    [ 'name' => 'comments', 'label' => ucwords(__('posts.comments')) ],
                ], // also for morphMany, hasManyThrough
                'hasOne' => [
                    //[ 'name' => 'child', 'label' => ucwords(__('posts.child')) ],
                ], // also for morphOne
            ]
        ];
    }

    /**
     * Visibles
     * @param  \Illuminate\Http\Request|null $request
     * @param Post $post
     * @return array
     */
    public static function visibles(Request $request = null, Post $post = null)
    {
        return [
            'index' => [
                'post' => [
                    [ 'name' => 'user', 'label' => ucwords(__('posts.user')), 'column' => 'name' ], // Only support belongsTo, hasOne
                    [ 'name' => 'post', 'label' => ucwords(__('posts.post')) ],
                ]
            ],
            'show' => [
                'post' => [
                    [ 'name' => 'user', 'label' => ucwords(__('posts.user')), 'column' => 'name' ], // Only support belongsTo, hasOne
                    [ 'name' => 'post', 'label' => ucwords(__('posts.post')) ],
                ]
            ]
        ];
    }

    /**
     * Fields
     * @param  \Illuminate\Http\Request|null $request
     * @param Post $post
     * @return array
     */
    public static function fields(Request $request = null, Post $post = null)
    {
        return [
            'create' => [
                'post' => [
                    //[ 'field' => 'select', 'name' => 'parent_id', 'label' => ucwords(__('posts.parent')), 'required' => true, 'options' => \App\Parent::filter()->get()->map(function ($parent) {
                    //    return [ 'value' => $parent->id, 'text' => $parent->name ];
                    //})->prepend([ 'value' => '', 'text' => '-' ])->toArray() ],
                    [ 'field' => 'input', 'type' => 'text', 'name' => 'post', 'label' => ucwords(__('posts.post')), 'required' => true ],
                ]
            ],
            'edit' => [
                'post' => [
                    //[ 'field' => 'select', 'name' => 'parent_id', 'label' => ucwords(__('posts.parent')), 'options' => \App\Parent::filter()->get()->map(function ($parent) {
                    //    return [ 'value' => $parent->id, 'text' => $parent->name ];
                    //})->prepend([ 'value' => '', 'text' => '-' ])->toArray() ],
                    [ 'field' => 'input', 'type' => 'text', 'name' => 'post', 'label' => ucwords(__('posts.post')) ],
                ]
            ]
        ];
    }

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
                //'parent_id' => 'required|exists:parents,id',
                'post' => 'required|string|max:255',
            ],
            'update' => [
                //'parent_id' => 'exists:parents,id',
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
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        $posts = Post::filter()
            ->paginate()->appends(request()->query());
        $this->authorize('index', 'App\Post');

        return response()->view('posts.index', [
            'posts' => $posts,
            'relations' => self::relations(request()),
            'visibles' => self::visibles(request())['index']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        $this->authorize('create', 'App\Post');

        return response()->view('posts.create', [
            'fields' => self::fields(request())['create']
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
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

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('posts.show', $post->getKey());

        return $response->withInput([ $post->getForeignKey() => $post->getKey() ])
            ->with('status', __('Success'));
    }

    /**
     * Display the specified resource.
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(Post $post)
    {
        $this->authorize('view', $post);

        return response()->view('posts.show', [
            'post' => $post,
            'relations' => self::relations(request(), $post),
            'visibles' => self::visibles(request(), $post)['show'],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return response()->view('posts.edit', [
            'post' => $post,
            'fields' => self::fields(request(), $post)['edit']
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Post $post
     * @return \Illuminate\Http\Response
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

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('posts.show', $post->getKey());

        return $response->withInput([ $post->getForeignKey() => $post->getKey() ])
            ->with('status', __('Success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Post $post
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();

        if (request()->filled('redirect') && starts_with(request()->redirect, request()->root()) && !str_contains(request()->redirect, '/posts/'.$post->getKey()))
            $response = response()->redirectTo(request()->redirect);
        else
            $response = response()->redirectToRoute('posts.index');

        return $response->with('status', __('Success'));
    }
}
