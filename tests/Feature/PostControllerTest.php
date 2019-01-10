<?php

namespace Tests\Feature;

use App\Post;
use Illuminate\Support\Facades\Route;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostControllerTest extends TestCase
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function testIndex()
    {
        if (!Route::has('posts.index')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $posts = factory(Post::class, 5)->create();

        $this->actingAs($user);
        $response = $this->get(route('posts.index'));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('posts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function testCreate()
    {
        if (!Route::has('posts.create')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->get(route('posts.create'));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function testStore()
    {
        if (!Route::has('posts.store')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->post(route('posts.store'), factory(Post::class)->make()->toArray());
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
        if (!Route::has('posts.show')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get(route('posts.show', [ $post->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('posts.show');
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function testEdit()
    {
        if (!Route::has('posts.edit')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get(route('posts.edit', [ $post->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('posts.edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function testUpdate()
    {
        if (!Route::has('posts.update')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->put(route('posts.update', [ $post->getKey() ]), factory(Post::class)->make()->toArray());
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
        if (!Route::has('posts.destroy')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->delete(route('posts.destroy', [ $post->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
    }
}
