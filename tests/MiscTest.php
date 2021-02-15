<?php

namespace StubKit\Tests;

use Illuminate\Support\Str;
use StubKit\Facades\StubKit;
use StubKit\Support\Item;

class MiscTest extends TestCase
{
    public function test_string_reset_macro()
    {
        $this->assertEquals('account user', Str::reset('AccountUser'));
    }

    // Belongs in syntax.
    public function test_variables_and_defaults_render()
    {
        config()->set([
            'stubkit.variables' => ['fields' => ['schema' => function ($fields) {
                return $fields;
            }]],
            'stubkit.variables.*' => [
                'happy' => function ($value) {
                    return $value.' is :)';
                },
            ],
        ]);

        StubKit::syntax(['user' => 'brian']);

        $this->assertCount(4, StubKit::variables());
    }

    // Belongs in syntax
    public function test_fields_render_replaces_variable_with_nothing_if_missing_console_option()
    {
        StubKit::syntax(['name' => 'AccountUser']);

        file_put_contents(base_path('missing-fields.txt'), "\n\t{{ fields.schema }}\n\n");

        StubKit::render();

        $this->assertEquals("\n\t\n\n", file_get_contents(base_path('missing-fields.txt')));
    }

    public function test_directive()
    {
        StubKit::syntax(['model' => 'User']);
        StubKit::activeField('created_at');

        $output = StubKit::directive('{{ ${{ model.camel }}->{{ field.snake }}->diffForHumans() }}');

        $this->assertEquals(
            '<?php echo \StubKit\Facades\StubKit::helper(\'@{{ $<:: model.camel ::>-><:: field.snake ::>->diffForHumans() }}\', get_defined_vars()); ?>',
            $output
        );
    }

    public function test_helper()
    {
        StubKit::syntax(['model' => 'AccountUser']);

        StubKit::activeField('created_at');

        $this->assertEquals(
            '{{ $accountUser->created_at->diffForHumans() }}',
            stubkit('{{ ${{ model.camel }}->{{ field.snake }}->diffForHumans() }}', [
                'model' => 'AccountUser',
                'field' => 'created_at',
            ])
        );
    }

    public function test_as_blade_variable()
    {
        $item = new Item('index', 'created_at', 'stubkit::index');

        $this->assertEquals(
            '{{ ${{ model.camel }}->created_at }}',
            $item->asBladeVariable()
        );
    }

    public function test_kitchen_sink()
    {
        $basepath = __DIR__.'/Fixtures/directory-1';
        $location = __DIR__.'/Fixtures/directory-1/sub-directory';
        $excluded = 'excluded-path';

        app()->setBasePath($basepath);

        StubKit::track();

        file_put_contents("${location}/test.txt", '{{ user.happy }}');
        file_put_contents("${basepath}/${excluded}/test.txt", '{{ user.happy }}');

        config()->set([
            'stubkit.excludes' => [$excluded],
            'stubkit.variables' => [],
            'stubkit.variables.*' => [
                'happy' => function ($value) {
                    return $value.' is :)';
                },
            ],
        ]);

        StubKit::syntax(['user' => 'brian']);

        StubKit::render();

        $this->assertCount(1, StubKit::rendered());
        $this->assertCount(2, StubKit::variables());
        $this->assertEquals("${location}/test.txt", StubKit::rendered()[0]);
        $this->assertEquals('brian is :)', file_get_contents("{$location}/test.txt"));
        $this->assertEquals('{{ user.happy }}', file_get_contents("${basepath}/${excluded}/test.txt"));
        $this->assertEquals('{{ user.happy }}', file_get_contents("${location}/existing.txt"));
        $this->assertEquals('{{ user.happy }}', file_get_contents("${basepath}/${excluded}/existing.txt"));

        unlink("${location}/test.txt");
        unlink("${basepath}/${excluded}/test.txt");
    }
}
