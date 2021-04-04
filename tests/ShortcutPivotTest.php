<?php

namespace StubKit\Tests;

use StubKit\Facades\StubKit;

class ShortcutPivotTest extends TestCase
{
    public function test_pivot_option_needs_two_models()
    {
        $this->artisan('make:scaffold Deal --pivot="User"')
            ->expectsOutput('Pivots require two comma separated models.')
            ->assertExitCode(1);
    }

    public function test_pivot_option_adds_columns_to_fields()
    {
        $this->artisan('make:scaffold Deal --pivot="User,Property"');
        $this->assertEquals('user_id, property_id', StubKit::fields());
        $this->artisan('make:scaffold Deal --pivot="User,Property" --fields="message, price"');
        $this->assertEquals('user_id, property_id, message, price', StubKit::fields());
    }

    public function test_pivot_option_adds_models_to_variables()
    {
        $this->artisan('make:scaffold Deal --pivot="User,Property"');
        $this->assertEquals('deals', StubKit::syntax()->get('model.lowerPlural'));
        $this->assertEquals('users', StubKit::syntax()->get('parent.lowerPlural'));
        $this->assertEquals('properties', StubKit::syntax()->get('child.lowerPlural'));
    }

    public function test_pivot_option_defaults_to_pivot_scaffold_type()
    {
        config()->set(['stubkit.scaffolds.pivot' => [
            'make:views Shop',
        ]]);

        $this->artisan('make:scaffold Deal --pivot="User,Property"');
        $this->assertFileExists(resource_path('views/shops/index.blade.php'));
    }

    public function test_pivot_option_creates_pivot_scaffolding()
    {
        $this->artisan('make:scaffold UserProperty --pivot="User,Property"');
        $this->assertFileExists(base_path('app/Http/Controllers/UserPropertyController.php'));
    }
}
