<?php

namespace StubKit\Tests;

use StubKit\Commands\ScaffoldMakeCommand;
use StubKit\Facades\StubKit;

class MakeScaffoldTest extends TestCase
{
    public function test_make_scaffold()
    {
        $this->artisan('make:scaffold Page --fields="name,age,published_at"');
        $this->assertTrue(in_array(base_path('routes/web.php'), StubKit::rendered()));
        $this->assertTrue(in_array(base_path('resources/views/pages/index.blade.php'), StubKit::rendered()));
    }

    public function test_make_scaffold_without_config()
    {
        config()->set(['stubkit.scaffolds' => []]);

        $this->artisan('make:scaffold User')
            ->expectsOutput('No scaffolds exist!')
            ->assertExitCode(1);
    }

    public function test_make_scaffold_type()
    {
        config()->set(['stubkit.scaffolds.default' => [
            'make:views Account',
        ]]);

        config()->set(['stubkit.scaffolds.api' => [
            'make:views User',
        ]]);

        $this->artisan('make:scaffold User --type=api');
        $this->assertFalse(is_dir(resource_path('/views/accounts')));
        $this->assertTrue(is_dir(resource_path('/views/users')));
    }

    public function test_migration_exception_handling()
    {
        $make = new ScaffoldMakeCommand();

        $this->assertEquals(
            "Table already exists!\n",
            $make->handleTableExists('class already exists.'),
        );
    }

    public function test_make_scaffold_with_fields()
    {
        $this->artisan('make:scaffold User --fields="id,name,email"');
        $this->assertFileExists(__DIR__.'/Fixtures/app/resources/views/users/index.blade.php');
        $content = file_get_contents(__DIR__.'/Fixtures/app/resources/views/users/index.blade.php');
        $this->assertStringContainsString('$user->id', $content);
        $this->assertStringContainsString('$user->name', $content);
        $this->assertStringContainsString('$user->email', $content);
    }

    public function test_file_count_scaffold_command()
    {
        config()->set(['stubkit.scaffolds.default' => [
            'make:views {{ scaffold.studly }}',
            'make:views Another{{ scaffold.studly }}',
        ]]);

        StubKit::track();

        $this->artisan('make:scaffold User');

        $this->assertCount(8, StubKit::rendered());
    }
}
