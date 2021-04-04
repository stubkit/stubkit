<?php

use Illuminate\Support\Str;
use StubKit\Facades\Fields;

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
        ],

        'api' => [
            'make:model {{ scaffold.studly }}',
            'make:controller Api\{{ scaffold.studly }}Controller --model={{ scaffold.studly }} --api',
            'make:resource {{ scaffold.studly }}Resource',
            'make:resource {{ scaffold.studly }}Collection',
            'make:migration create_{{scaffold.snakePlural}}_table',
            'migrate',
            'db:seed --class={{ scaffold.studly }}Seeder',
            'test --filter={{ scaffold.studly }}Test',
        ],

        'pivot' => [
            'make:model {{model.studly}}',
            'make:routes {{model.studly}} --type=pivot --to=web',
            'make:controller {{model.studly}}Controller --type=pivot',
            'make:migration create_{{parent.snake}}_{{child.snake}}_table',
        ],

        'nested' => [
            'make:model {{model.studly}}',
            'make:request Create{{ scaffold.studly }}Request',
            'make:request Update{{ scaffold.studly }}Request',
            'make:controller {{scaffold.studly}}Controller --type=nested',
            'make:routes {{scaffold.studly}} --type=nested --to=web',
            'make:migration create_{{scaffold.snakePlural}}_table',
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

            'lowerPlural' => function ($value) {
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
            'schema' => function ($fields) {
                return Fields::render('schema', $fields);
            },
            'faker' => function ($fields) {
                return Fields::render('faker', $fields);
            },
            'rules' => function ($fields) {
                return Fields::render('rules', $fields);
            },
            'index' => function ($fields) {
                return Fields::render('index', $fields);
            },
            'show' => function ($fields) {
                return Fields::render('show', $fields);
            },
            'create' => function ($fields) {
                return Fields::render('create', $fields);
            },
            'edit' => function ($fields) {
                return Fields::render('edit', $fields);
            },
            'headings' => function ($fields) {
                return view('stubkit::formats.headings')
                    ->with(['fields' => $fields]);
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

    /*
   |--------------------------------------------------------------------------
   | Views
   |--------------------------------------------------------------------------
   | These settings refer to the make:views command.
   |--------------------------------------------------------------------------
   */
    'views' => [
        'path' => 'resources/views/{{model.slugPlural}}/{{view.slug}}.blade.php',
        'stubs' => ['index', 'create', 'show', 'edit'],
    ],
];
