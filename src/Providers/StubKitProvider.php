<?php

namespace StubKit\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use StubKit\Commands\InstallCommand;
use StubKit\Commands\RoutesMakeCommand;
use StubKit\Commands\ScaffoldMakeCommand;
use StubKit\Commands\ViewsMakeCommand;
use StubKit\StubKit;
use StubKit\Support\Fields;

class StubKitProvider extends ServiceProvider
{
    /**
     * Boot the provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                ScaffoldMakeCommand::class,
                RoutesMakeCommand::class,
                ViewsMakeCommand::class,
            ]);
        }

        $this->app->singleton(StubKit::class, function () {
            return new StubKit;
        });

        $this->app->bind(Fields::class, function () {
            return new Fields(
                config('stubkit-fields'),
                config('stubkit-mappings'),
            );
        });

        $this->loadViewsFrom(__DIR__.'/../../views', 'stubkit');

        $this->publishes([
            __DIR__.'/../../config/stubkit.php' => config_path('stubkit.php'),
        ], 'stubkit-config');

        $this->publishes([
            __DIR__.'/../../config/fields.php' => config_path('stubkit-fields.php'),
        ], 'stubkit-fields');

        $this->publishes([
            __DIR__.'/../../config/mappings.php' => config_path('stubkit-mappings.php'),
        ], 'stubkit-mappings');

        $this->publishes([
            __DIR__.'/../../stubs/routes.web.stub' => base_path('stubs/routes.web.stub'),
            __DIR__.'/../../stubs/routes.api.stub' => base_path('stubs/routes.api.stub'),
            __DIR__.'/../../stubs/view.create.stub' => base_path('stubs/view.create.stub'),
            __DIR__.'/../../stubs/view.edit.stub' => base_path('stubs/view.edit.stub'),
            __DIR__.'/../../stubs/view.index.stub' => base_path('stubs/view.index.stub'),
            __DIR__.'/../../stubs/view.show.stub' => base_path('stubs/view.show.stub'),
        ], 'stubkit-stubs');

        $this->publishes([
            __DIR__.'/../../stubs/controller.model.api.stub' => base_path('stubs/controller.model.api.stub'),
            __DIR__.'/../../stubs/controller.model.stub' => base_path('stubs/controller.model.stub'),
            __DIR__.'/../../stubs/factory.stub' => base_path('stubs/factory.stub'),
            __DIR__.'/../../stubs/migration.create.stub' => base_path('stubs/migration.create.stub'),
            __DIR__.'/../../stubs/request.stub' => base_path('stubs/request.stub'),
            __DIR__.'/../../stubs/test.stub' => base_path('stubs/test.stub'),
        ], 'stubkit-stub-overrides');

        Str::macro('reset', function ($value) {
            return Str::of($value)->snake()->replace('_', ' ')->singular();
        });

        Blade::directive('stubkit', function ($expression) {
            return \StubKit\Facades\StubKit::directive($expression);
        });
    }

    /**
     * Register the provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);

        $this->mergeConfigFrom(__DIR__.'/../../config/stubkit.php', 'stubkit');
        $this->mergeConfigFrom(__DIR__.'/../../config/fields.php', 'stubkit-fields');
        $this->mergeConfigFrom(__DIR__.'/../../config/mappings.php', 'stubkit-mappings');
    }
}
