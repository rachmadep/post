<?php

namespace Tests\Feature\Api\Post;

use App\Comment;
use Illuminate\Support\Facades\Route;
use App\Post;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentControllerTest extends TestCase
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function testIndex()
    {
        if (!Route::has('api.posts.comments.index')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $comments = $post->comments()->saveMany(factory(Comment::class, 5)->make([ 'post_'.$post->getKeyName() => $post->getKey() ]));

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('api.posts.comments.index', [ $post->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function testStore()
    {
        if (!Route::has('api.posts.comments.store')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();

        $this->actingAs($user, 'api');
        $response = $this->postJson(route('api.posts.comments.store', [ $post->getKey() ]), factory(Comment::class)->make([ $post->getForeignKey() => $post->getKey() ])->toArray());
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function testShow()
    {
        if (!Route::has('api.posts.comments.show')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $comment = $post->comments()->save(factory(Comment::class)->make([ 'post_'.$post->getKeyName() => $post->getKey(), 'user_id' => $user->id ]));

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('api.posts.comments.show', [ $post->getKey(), $comment->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function testUpdate()
    {
        if (!Route::has('api.posts.comments.update')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $comment = $post->comments()->save(factory(Comment::class)->make([ 'post_'.$post->getKeyName() => $post->getKey(), 'user_id' => $user->id ]));

        $this->actingAs($user, 'api');
        $response = $this->putJson(route('api.posts.comments.update', [ $post->getKey(), $comment->getKey()  ]), factory(Comment::class)->make([ $post->getForeignKey() => $post->getKey() ])->toArray());
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    public function testDestroy()
    {
        if (!Route::has('api.posts.comments.destroy')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $comment = $post->comments()->save(factory(Comment::class)->make([ 'post_'.$post->getKeyName() => $post->getKey(), 'user_id' => $user->id ]));

        $this->actingAs($user, 'api');
        $response = $this->deleteJson(route('api.posts.comments.destroy', [ $post->getKey(), $comment->getKey()  ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }
}
