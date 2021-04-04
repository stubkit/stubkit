<?php

namespace StubKit\Tests;

use StubKit\Facades\StubKit;

class ShortcutNestedTest extends TestCase
{
    public function test_nested_option_adds_columns_to_fields()
    {
        $this->artisan('make:scaffold Neighborhood --nested="City"');
        $this->assertEquals('city_id', StubKit::fields());
        $this->artisan('make:scaffold Neighborhood --nested="City" --fields="message, price"');
        $this->assertEquals('message, price, city_id', StubKit::fields());
    }

    public function test_nested_option_adds_models_to_variables()
    {
        $this->artisan('make:scaffold Neighborhood --nested="City"');
        $this->assertEquals('neighborhoods', StubKit::syntax()->get('model.lowerPlural'));
        $this->assertEquals('cities', StubKit::syntax()->get('parent.lowerPlural'));
    }

    public function test_nested_option_defaults_to_nested_scaffold_type()
    {
        config()->set(['stubkit.scaffolds.nested' => [
            'make:views Shop',
        ]]);

        $this->artisan('make:scaffold Neighborhood --nested="City"');
        $this->assertFileExists(resource_path('views/shops/index.blade.php'));
    }

    public function test_nested_option_creates_nested_scaffolding()
    {
        $this->artisan('make:scaffold Neighborhood --nested="City"');
        $this->assertFileExists(base_path('app/Http/Controllers/NeighborhoodController.php'));
    }
}
