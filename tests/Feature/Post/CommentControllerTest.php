<?php

namespace Tests\Feature\Post;

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
        if (!Route::has('posts.comments.index')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $comments = $post->comments()->saveMany(factory(Comment::class, 5)->make([ $post->getForeignKey() => $post->getKey() ]));

        $this->actingAs($user);
        $response = $this->get(route('posts.comments.index', [ $post->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('posts.comments.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function testCreate()
    {
        if (!Route::has('posts.comments.create')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();

        $this->actingAs($user);
        $response = $this->get(route('posts.comments.create', [ $post->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('posts.comments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function testStore()
    {
        if (!Route::has('posts.comments.store')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();

        $this->actingAs($user);
        $response = $this->post(route('posts.comments.store', [ $post->getKey() ]), factory(Comment::class)->make([ $post->getForeignKey() => $post->getKey() ])->toArray());
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function testShow()
    {
        if (!Route::has('posts.comments.show')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $comment = $post->comments()->save(factory(Comment::class)->make([ $post->getForeignKey() => $post->getKey() ]));

        $this->actingAs($user);
        $response = $this->get(route('posts.comments.show', [ $post->getKey(), $comment->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('posts.comments.show');
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function testEdit()
    {
        if (!Route::has('posts.comments.edit')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $comment = $post->comments()->save(factory(Comment::class)->make([ $post->getForeignKey() => $post->getKey() ]));

        $this->actingAs($user);
        $response = $this->get(route('posts.comments.edit', [ $post->getKey(), $comment->getKey()  ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('posts.comments.edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function testUpdate()
    {
        if (!Route::has('posts.comments.update')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $comment = $post->comments()->save(factory(Comment::class)->make([ $post->getForeignKey() => $post->getKey() ]));

        $this->actingAs($user);
        $response = $this->put(route('posts.comments.update', [ $post->getKey(), $comment->getKey()  ]), factory(Comment::class)->make([ $post->getForeignKey() => $post->getKey() ])->toArray());
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return void
     */
    public function testDestroy()
    {
        if (!Route::has('posts.comments.destroy')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create();
        $comment = $post->comments()->save(factory(Comment::class)->make([ $post->getForeignKey() => $post->getKey() ]));

        $this->actingAs($user);
        $response = $this->delete(route('posts.comments.destroy', [ $post->getKey(), $comment->getKey()  ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
    }
}
