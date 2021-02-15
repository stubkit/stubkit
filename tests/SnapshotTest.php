<?php

namespace StubKit\Tests;

use Illuminate\Support\Facades\File;
use StubKit\Facades\StubKit;

class SnapshotTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::copyDirectory(
            __DIR__.'/Fixtures/snapshots',
            base_path('snapshots'),
        );
    }

    public function test_index_snapshot()
    {
        StubKit::syntax(['model' => 'Post']);

        StubKit::assertRender(function () {
            return view('stubkit::index', [
                'fields' => 'id,first_name,last_name,created_at',
            ]);
        }, base_path('snapshots/index.blade.php'));
    }

    public function test_show_snapshot()
    {
        StubKit::syntax(['model' => 'Post']);

        StubKit::assertRender(function () {
            return view('stubkit::show', [
                'fields' => 'id,first_name,last_name',
            ]);
        }, base_path('snapshots/show.blade.php'));
    }

    public function test_create_snapshot()
    {
        StubKit::syntax(['model' => 'TeamMember']);

        StubKit::assertRender(function () {
            return view('stubkit::create', [
                'fields' => 'avatar,first_name,bio',
            ]);
        }, base_path('snapshots/create.blade.php'));
    }

    public function test_edit_snapshot()
    {
        StubKit::syntax(['model' => 'TeamMember']);

        StubKit::assertRender(function () {
            return view('stubkit::edit', [
                'fields' => 'avatar,first_name,bio',
            ]);
        }, base_path('snapshots/edit.blade.php'));
    }

    public function test_faker_snapshot()
    {
        StubKit::syntax(['model' => 'Post']);

        StubKit::assertRender(function () {
            return view('stubkit::faker', [
                'fields' => 'file,description,size',
            ]);
        }, base_path('snapshots/faker.blade.php'));
    }

    public function test_rules_snapshot()
    {
        StubKit::syntax(['model' => 'Post']);

        StubKit::assertRender(function () {
            return view('stubkit::rules', [
                'fields' => 'file,description,size',
            ]);
        }, base_path('snapshots/rules.blade.php'));
    }

    public function test_schema_snapshot()
    {
        StubKit::syntax(['model' => 'Post']);

        StubKit::assertRender(function () {
            return view('stubkit::schema', [
                'fields' => 'file,description,size',
            ]);
        }, base_path('snapshots/schema.blade.php'));
    }

    public function test_directive_snapshot()
    {
        $view = __DIR__.'/../views/directive-test.blade.php';

        file_put_contents($view, '@stubkit(\'{{ ${{ model.camel }}->{{ field.snake }}->diffForHumans() }}\')');

        StubKit::syntax(['model' => 'Post']);
        StubKit::activeField('created_at');

        StubKit::assertRender(function () {
            return view('stubkit::directive-test', ['field' => 'created_at']);
        }, base_path('snapshots/directive.blade.php'));

        unlink($view);
    }
}
