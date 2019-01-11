<?php

namespace Tests\Feature;

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
        if (!Route::has('users.index')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $users = factory(User::class, 5)->create();

        $this->actingAs($user);
        $response = $this->get(route('users.index'));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return void
     */
    public function testCreate()
    {
        if (!Route::has('users.create')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->get(route('users.create'));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return void
     */
    public function testStore()
    {
        if (!Route::has('users.store')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->post(route('users.store'), factory(User::class)->make()->toArray());
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
        if (!Route::has('users.show')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->get(route('users.show', [ $user->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.show');
    }

    /**
     * Display the specified resource.
     *
     * @return void
     */
    public function testEdit()
    {
        if (!Route::has('users.edit')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->get(route('users.edit', [ $user->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertViewIs('users.edit');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return void
     */
    public function testUpdate()
    {
        if (!Route::has('users.update')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->put(route('users.update', [ $user->getKey() ]), factory(User::class)->make()->toArray());
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
        if (!Route::has('users.destroy')) { $this->expectNotToPerformAssertions(); return; }
        $user = factory(User::class)->create();

//        $user = factory(User::class)->create();

        $this->actingAs($user);
        $response = $this->delete(route('users.destroy', [ $user->getKey() ]));
        if ($response->exception) {
            $this->expectOutputString('');
            $this->setOutputCallback(function () use($response) { return $response->exception; });
            return;
        }
        $response->assertSessionMissing('errors');
        $response->assertStatus(302);
    }
}
