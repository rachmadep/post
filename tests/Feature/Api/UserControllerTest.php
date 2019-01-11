<?php

namespace Tests\Feature\Api;

use App\User;
use Illuminate\Support\Facades\Route;
//use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function testIndex()
    {
        if (!Route::has('api.users.index')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $users = factory(User::class, 5)->create();

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('api.users.index'));
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
        if (!Route::has('api.users.store')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');
        $response = $this->postJson(route('api.users.store'), factory(User::class)->make()->toArray());
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
        if (!Route::has('api.users.show')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');
        $response = $this->getJson(route('api.users.show', [ $user->getKey() ]));
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
        if (!Route::has('api.users.update')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');
        $response = $this->putJson(route('api.users.update', [ $user->getKey() ]), factory(User::class)->make()->toArray());
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
        if (!Route::has('api.users.destroy')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();

        $this->actingAs($user, 'api');
        $response = $this->deleteJson(route('api.users.destroy', [ $user->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSuccessful();
    }
}
