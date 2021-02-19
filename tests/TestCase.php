<?php

namespace StubKit\Tests;

use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase as BaseCase;
use StubKit\Facades\StubKit;
use StubKit\Providers\StubKitProvider;

class TestCase extends BaseCase
{
    protected function getPackageProviders($app)
    {
        return [StubKitProvider::class];
    }

    protected function setUp():void
    {
        parent::setUp();

        $this->addFakeAppStuff();

        File::deleteDirectory(__DIR__.'/../vendor/orchestra/testbench-core/laravel/storage/framework/views');

        mkdir(__DIR__.'/../vendor/orchestra/testbench-core/laravel/storage/framework/views');
    }

    protected function tearDown() :void
    {
        File::deleteDirectory(__DIR__.'/Fixtures/app');

        File::deleteDirectory(__DIR__.'/../vendor/orchestra/testbench-core/laravel/storage/framework/views');

        mkdir(__DIR__.'/../vendor/orchestra/testbench-core/laravel/storage/framework/views');
    }

    public function addFakeAppStuff()
    {
        StubKit::ignore(__DIR__.'/Fixtures/app/routes/web.php');
        StubKit::ignore(__DIR__.'/Fixtures/app/routes/api.php');
        StubKit::ignore(__DIR__.'/Fixtures/app/composer.json');

        app()->setBasePath(__DIR__.'/Fixtures/app');
        config()->set(['stubkit.scaffold-delay' => 0]);

        File::deleteDirectory(__DIR__.'/Fixtures/app');

        config()->set(['stubkit.scaffolds.default' => [
            'make:views {{ scaffold.studly }}',
            'make:routes {{ scaffold.studly }}',
        ]]);

        config()->set(['stubkit.scaffold-completed' => []]);
        config()->set(['stubkit.scaffold-delay' => 0]);

        mkdir(__DIR__.'/Fixtures/app');
        mkdir(__DIR__.'/Fixtures/app/routes');
        mkdir(__DIR__.'/Fixtures/app/resources');
        mkdir(__DIR__.'/Fixtures/app/resources/views');
        mkdir(__DIR__.'/Fixtures/app/database');
        mkdir(__DIR__.'/Fixtures/app/database/migrations');

        file_put_contents(__DIR__.'/Fixtures/app/routes/web.php', '');
        file_put_contents(__DIR__.'/Fixtures/app/routes/api.php', '');

        file_put_contents(__DIR__.'/Fixtures/app/composer.json', json_encode([
            'autoload' => [
                'psr-4' => [
                    'Testing\\' => realpath(base_path()),
                ],
            ],
        ]));
    }
}
