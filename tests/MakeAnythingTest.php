<?php

namespace StubKit\Tests;

use StubKit\Facades\StubKit;

class MakeAnythingTest extends TestCase
{
    public function test_laravel_commands_with_fields()
    {
        mkdir(base_path('stubs'));

        file_put_contents(base_path('stubs/factory.stub'), '{{model.studly}}::class  {{ fields.faker }}');

        StubKit::track();

        $this->artisan('make:factory UserTest --model=User --fields="id,name,age"');

        $this->assertTrue(in_array(base_path('database/factories/UserTestFactory.php'), StubKit::rendered()));

        $this->assertStringContainsString(
            '$this->faker->randomNumber',
            file_get_contents(base_path('database/factories/UserTestFactory.php'))
        );
    }

    public function test_commands_will_not_throw_invalid_options_exception()
    {
        config()->set('stubkit.commands', ['make:test']);

        $worked = false;

        try {
            $this->artisan('make:test User --invalid-option');
            $worked = true;
        } catch (\Exception $e) {
            $this->assertFalse(true); // doesn't get called
        }

        $this->assertTrue($worked);
        $this->assertFalse(StubKit::allows('make:test'));
    }

    public function test_commands_not_allowed_will_throw_invalid_options_exception()
    {
        config()->set('stubkit.commands', []);

        $worked = false;

        try {
            $this->artisan('make:test User --invalid-option');
            $worked = true;
        } catch (\Exception $e) {
        }

        $this->assertTrue(StubKit::allows(null)); // `php artisan`
        $this->assertTrue(StubKit::allows('make:test'));
        $this->assertFalse($worked);
    }
}
