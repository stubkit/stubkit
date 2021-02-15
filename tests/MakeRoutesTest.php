<?php

namespace StubKit\Tests;

use StubKit\Facades\StubKit;

class MakeRoutesTest extends TestCase
{
    public function test_makes_web_routes()
    {
        $this->assertStringEqualsFile(
            __DIR__.'/Fixtures/app/routes/web.php',
            ''
        );

        $this->artisan('make:routes User')
            ->expectsOutput('Routes created successfully.');

        $this->assertStringContainsString(
            "Route::get('users', 'UserController@index')->name('users.index');",
            file_get_contents(__DIR__.'/Fixtures/app/routes/web.php')
        );
    }

    public function test_make_api_routes()
    {
        $this->assertStringEqualsFile(
            __DIR__.'/Fixtures/app/routes/api.php',
            ''
        );

        $this->artisan('make:routes User --api')
            ->expectsOutput('Routes created successfully.');

        $this->assertStringContainsString(
            "Route::get('users', 'UserController@index')->name('users.index');",
            file_get_contents(__DIR__.'/Fixtures/app/routes/api.php')
        );
    }

    public function test_routes_do_not_repeat()
    {
        file_put_contents(__DIR__.'/Fixtures/app/routes/api.php', "Route::get('users', 'UserController@index')->name('users.index');");

        $this->artisan('make:routes User --api')
            ->expectsOutput('Routes already exists!')
            ->assertExitCode(1);
    }
}
