<?php

namespace StubKit\Tests;

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

        $this->artisan('make:routes User --type=api')
            ->expectsOutput('Routes created successfully.');

        $this->assertStringContainsString(
            "Route::get('users', 'UserController@index')->name('users.index');",
            file_get_contents(__DIR__.'/Fixtures/app/routes/api.php')
        );
    }

    public function test_routes_do_not_repeat()
    {
        file_put_contents(__DIR__.'/Fixtures/app/routes/api.php', "Route::get('users', 'UserController@index')->name('users.index');");

        $this->artisan('make:routes User --type=api')
            ->expectsOutput('Routes already exists!')
            ->assertExitCode(1);
    }

    public function test_using_option_to()
    {
        file_put_contents(__DIR__.'/Fixtures/app/routes/custom.php', '');

        $this->artisan('make:routes User --to=custom')
            ->assertExitCode(0);

        $this->assertStringContainsString(
            'UserController',
            file_get_contents(__DIR__.'/Fixtures/app/routes/custom.php')
        );
    }

    public function test_using_option_stub()
    {
        mkdir(__DIR__.'/Fixtures/app/stubs/');

        file_put_contents(__DIR__.'/Fixtures/app/stubs/routes.custom.stub', '{{model.title}} works');

        $this->artisan('make:routes User --stub=custom')
            ->assertExitCode(0);

        $this->assertStringContainsString(
            'User works',
            file_get_contents(__DIR__.'/Fixtures/app/routes/web.php')
        );
    }
}
