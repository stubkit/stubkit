<?php

namespace StubKit\Tests;

use StubKit\Facades\StubKit;
use StubKit\Support\Fields;

class PackageTest extends TestCase
{
    public function test_stub_discovery()
    {
        mkdir(base_path('package-path'));

        file_put_contents(base_path('package-path/some.stub'), 'content');

        $publishes = StubKit::discover(base_path('package-path'));

        $this->assertEquals([
            base_path('package-path/some.stub') => base_path('stubs/some.stub'),
        ], $publishes);
    }

    public function test_package_adding_field_types()
    {
        StubKit::addTypes([
            'default' => [
                'custom' => 'custom-default',
            ],
            'string' => [
                'custom' => 'custom-string',
            ],
        ]);

        $this->assertEquals('custom-string', config('stubkit-fields.string.custom'));
        $this->assertEquals('custom-default', config('stubkit-fields.default.custom'));
    }

    public function test_package_adding_variable()
    {
        StubKit::variable('fields.nova', function ($fields) {
            return "${fields} for nova";
        });

        StubKit::track();

        StubKit::syntax(['fields' => 'id,name,email']);

        file_put_contents(base_path('nova.txt'), '{{ fields.nova }}');

        StubKit::render();

        $content = file_get_contents(base_path('nova.txt'));

        $this->assertEquals('id,name,email for nova', $content);
    }

    public function test_field_render_helper()
    {
        $rendered = Fields::render('rules', 'name,email');

        $this->assertEquals("'name' => 'required',\n'email' => 'required|email',\n", $rendered);
    }
}
