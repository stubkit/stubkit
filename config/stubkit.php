<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Scaffolds
    |--------------------------------------------------------------------------
    | Groups of commands to run together when php artisan scaffold is run
    | Without a --type=<name>, * group will be used as a default type
    | You can add different groups of commands for different needs.
    |--------------------------------------------------------------------------
    */

    'scaffolds' => [

        'default' => [
            'make:model {{ scaffold.studly }}',
            'make:controller {{ scaffold.studly }}Controller --model={{ scaffold.studly }}',
            'make:views {{ scaffold.studly }}',
            'make:routes {{ scaffold.studly }}',
            'make:request Create{{ scaffold.studly }}Request',
            'make:request Update{{ scaffold.studly }}Request',
            'make:test {{ scaffold.studly }}Test',
            'make:factory {{ scaffold.studly }}Factory',
            'make:seeder {{ scaffold.studly }}Seeder',
            'make:migration create_{{ scaffold.snakePlural }}_table',
            'migrate',
            'db:seed --class={{ scaffold.studly }}Seeder',
            'test --filter={{ scaffold.studly }}Test',
        ],

        'api' => [
            'make:model {{ scaffold.studly }}',
            'make:controller Api\{{ scaffold.studly }}Controller --model={{ scaffold.studly }} --api',
            'make:resource {{ scaffold.studly }}Resource',
            'make:resource {{ scaffold.studly }}Collection',
            'migrate',
            'db:seed --class={{ scaffold.studly }}Seeder',
            'test --filter={{ scaffold.studly }}Test',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Variables
    |--------------------------------------------------------------------------
    |
    */

    'variables' => [

        '*' => [

            'lower' => function ($value) {
                return Str::reset($value);
            },

            'title' => function ($value) {
                return Str::reset($value)->title();
            },

            'studly' => function ($value) {
                return Str::reset($value)->studly();
            },

            'camel' => function ($value) {
                return Str::reset($value)->camel();
            },

            'slug' => function ($value) {
                return Str::reset($value)->slug();
            },

            'snake' => function ($value) {
                return Str::reset($value)->snake();
            },

            'plural' => function ($value) {
                return Str::reset($value)->plural();
            },

            'titlePlural' => function ($value) {
                return Str::reset($value)->plural()->title();
            },

            'studlyPlural' => function ($value) {
                return Str::reset($value)->plural()->studly();
            },

            'camelPlural' => function ($value) {
                return Str::reset($value)->plural()->camel();
            },

            'slugPlural' => function ($value) {
                return Str::reset($value)->plural()->slug();
            },

            'snakePlural' => function ($value) {
                return Str::reset($value)->plural()->snake();
            },
        ],

        'app' => [
            'url' => function () {
                return url();
            },
            'namespace' => function () {
                return app()->getNamespace();
            },
        ],

        'fields' => [
            'schema' => function ($value) {
                return view('stubkit::schema')->with(['fields' => $value]);
            },
            'faker' => function ($value) {
                return view('stubkit::faker')->with(['fields' => $value]);
            },
            'rules' => function ($value) {
                return view('stubkit::rules')->with(['fields' => $value]);
            },
            'index' => function ($value) {
                return view('stubkit::index')->with(['fields' => $value]);
            },
            'show' => function ($value) {
                return view('stubkit::show')->with(['fields' => $value]);
            },
            'create' => function ($value) {
                return view('stubkit::create')->with(['fields' => $value]);
            },
            'edit' => function ($value) {
                return view('stubkit::edit')->with(['fields' => $value]);
            },
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Commands
    |--------------------------------------------------------------------------
    | These commands will trigger StubKit.. all others will be ignored.
    |--------------------------------------------------------------------------
    */

    'commands' => [
        'make:cast',
        'make:channel',
        'make:command',
        'make:component',
        'make:controller',
        'make:event',
        'make:exception',
        'make:factory',
        'make:job',
        'make:listener',
        'make:mail',
        'make:middleware',
        'make:migration',
        'make:model',
        'make:notification',
        'make:observer',
        'make:policy',
        'make:provider',
        'make:request',
        'make:rule',
        'make:seeder',
        'make:test',
        'make:scaffold',
        'make:routes',
        'make:views',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excludes
    |--------------------------------------------------------------------------
    | These relative sub folders to ignore when file changes are gathered.
    |--------------------------------------------------------------------------
    */

    'excludes' => [
        'public',
        'vendor',
        'storage',
        'bootstrap',
    ],

];
