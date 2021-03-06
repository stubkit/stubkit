<?php

namespace StubKit\Tests;

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
        $view = file_get_contents(base_path('resources/views/pages/index.blade.php'));
        $this->assertStringContainsString('{{ $page->name }}', $view);
        $this->assertStringContainsString('{{ $page->age }}', $view);
        $this->assertStringContainsString('title="{{ $page->published_at }}"', $view);
        $this->assertStringContainsString('{{ $page->published_at->diffForHumans() }}', $view);
    }

    public function test_make_views_with_view_flag()
    {
        $this->assertFileDoesNotExist(
            __DIR__.'/Fixtures/app/resources/views/users/index.blade.php'
        );

        $this->artisan('make:views User --type=index --type=show')
            ->expectsOutput('2 views created successfully.');

        $this->assertFileExists(__DIR__.'/Fixtures/app/resources/views/users/index.blade.php');
        $this->assertFileExists(__DIR__.'/Fixtures/app/resources/views/users/show.blade.php');
        $this->assertFileDoesNotExist(__DIR__.'/Fixtures/app/resources/views/users/create.blade.php');
        $this->assertFileDoesNotExist(__DIR__.'/Fixtures/app/resources/views/users/edit.blade.php');
    }

    public function test_make_views_when_already_exists()
    {
        mkdir(__DIR__.'/Fixtures/app/resources/views/users', 0777, true);
        file_put_contents(__DIR__.'/Fixtures/app/resources/views/users/index.blade.php', '');

        $this->artisan('make:views User')
            ->expectsOutput('Views already exists!')
            ->assertExitCode(1);
    }

    public function test_view_path_using_variables()
    {
        config()->set('stubkit.views.stubs', ['create-{{model.studly}}-form']);
        config()->set('stubkit.views.path', 'resources/js/Pages/{{model.studlyPlural}}/{{view.studly}}.vue');

        mkdir(__DIR__.'/Fixtures/app/stubs');
        file_put_contents(__DIR__.'/Fixtures/app/stubs/view.create-{{model.studly}}-form.stub', '');
        $this->artisan('make:views User');

        $this->assertFileExists(base_path('resources/js/Pages/Users/CreateUserForm.vue'));
    }

    public function test_view_stubs_has_config_and_missing_actual_stub()
    {
        config()->set('stubkit.views.stubs', ['create-{{model.studly}}-form']);

        $this->artisan('make:views User');

        $this->assertFileExists(base_path('resources/views/users/create-user-form.blade.php'));
        $this->assertEquals('', file_get_contents(base_path('resources/views/users/create-user-form.blade.php')));
    }
}
