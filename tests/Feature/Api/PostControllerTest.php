<?php

namespace Tests\Feature\Api;

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
        if (!Route::has('api.posts.index')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $posts = factory(Post::class, 5)->create();

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('api.posts.index'));
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
        if (!Route::has('api.posts.store')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');
        $response = $this->postJson(route('api.posts.store'), factory(Post::class)->make()->toArray());
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
        if (!Route::has('api.posts.show')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('api.posts.show', [ $post->getKey() ]));
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
        if (!Route::has('api.posts.update')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $this->actingAs($user, 'api');
        $response = $this->putJson(route('api.posts.update', [ $post->getKey() ]), factory(Post::class)->make()->toArray());
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
        if (!Route::has('api.posts.destroy')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $this->actingAs($user, 'api');
        $response = $this->deleteJson(route('api.posts.destroy', [ $post->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }
}
