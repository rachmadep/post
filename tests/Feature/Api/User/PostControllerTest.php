<?php

namespace Tests\Feature\Api\User;

use App\Post;
use Illuminate\Support\Facades\Route;
use App\User;
//use App\User;
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
        if (!Route::has('api.users.posts.index')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();
        $posts = $user->posts()->saveMany(factory(Post::class, 5)->make([ 'user_'.$user->getKeyName() => $user->getKey() ]));

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('api.users.posts.index', [ $user->getKey() ]));
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
        if (!Route::has('api.users.posts.store')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');
        $response = $this->postJson(route('api.users.posts.store', [ $user->getKey() ]), factory(Post::class)->make([ $user->getForeignKey() => $user->getKey() ])->toArray());
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
        if (!Route::has('api.users.posts.show')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();
        $post = $user->posts()->save(factory(Post::class)->make([ 'user_'.$user->getKeyName() => $user->getKey() ]));

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('api.users.posts.show', [ $user->getKey(), $post->getKey() ]));
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
        if (!Route::has('api.users.posts.update')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();
        $post = $user->posts()->save(factory(Post::class)->make([ 'user_'.$user->getKeyName() => $user->getKey() ]));

        $this->actingAs($user, 'api');
        $response = $this->putJson(route('api.users.posts.update', [ $user->getKey(), $post->getKey()  ]), factory(Post::class)->make([ $user->getForeignKey() => $user->getKey() ])->toArray());
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
        if (!Route::has('api.users.posts.destroy')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();
        $post = $user->posts()->save(factory(Post::class)->make([ 'user_'.$user->getKeyName() => $user->getKey() ]));

        $this->actingAs($user, 'api');
        $response = $this->deleteJson(route('api.users.posts.destroy', [ $user->getKey(), $post->getKey()  ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }
}
