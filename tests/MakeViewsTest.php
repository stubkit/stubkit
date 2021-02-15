<?php

namespace StubKit\Tests;

use StubKit\Facades\StubKit;

class MakeViewsTest extends TestCase
{
    public function test_make_views()
    {
        $this->assertFileDoesNotExist(
            __DIR__.'/Fixtures/app/resources/views/users/index.blade.php'
        );

        $this->artisan('make:views User --fields="name"')
            ->expectsOutput('4 views created successfully.');

        $this->assertFileExists(__DIR__.'/Fixtures/app/resources/views/users/index.blade.php');
        $this->assertFileExists(__DIR__.'/Fixtures/app/resources/views/users/show.blade.php');
        $this->assertFileExists(__DIR__.'/Fixtures/app/resources/views/users/create.blade.php');
        $this->assertFileExists(__DIR__.'/Fixtures/app/resources/views/users/edit.blade.php');

        $indexContent = file_get_contents(__DIR__.'/Fixtures/app/resources/views/users/index.blade.php');

        $this->assertStringContainsString('$user->name', $indexContent);
    }

    public function test_make_views_with_fields()
    {
        $this->artisan('make:views Page --fields="name,age,published_at"');
        $route = file_get_contents(base_path('routes/web.php'));
        $view = file_get_contents(base_path('resources/views/pages/index.blade.php'));
        $this->assertStringContainsString('{{ $page->name }}', $view);
        $this->assertStringContainsString('{{ $page->age }}', $view);
        $this->assertStringContainsString('title="{{ $page->published_at }}"', $view);
        $this->assertStringContainsString('{{ $page->published_at->diffForHumans() }}', $view);
    }

    public function test_make_views_with_file_flag()
    {
        $this->assertFileDoesNotExist(
            __DIR__.'/Fixtures/app/resources/views/users/index.blade.php'
        );

        $this->artisan('make:views User --index')
            ->expectsOutput('1 view created successfully.');

        $this->assertFileExists(__DIR__.'/Fixtures/app/resources/views/users/index.blade.php');
        $this->assertFileDoesNotExist(__DIR__.'/Fixtures/app/resources/views/users/show.blade.php');
        $this->assertFileDoesNotExist(__DIR__.'/Fixtures/app/resources/views/users/create.blade.php');
        $this->assertFileDoesNotExist(__DIR__.'/Fixtures/app/resources/views/users/edit.blade.php');
    }

    public function test_make_views_when_already_exists()
    {
        mkdir(__DIR__.'/Fixtures/app/resources/views/users');

        $this->artisan('make:views User')
            ->expectsOutput('Views already exists!')
            ->assertExitCode(1);
    }
}
