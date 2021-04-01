<?php

namespace StubKit\Tests;

use Exception;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use StubKit\Support\Fields;
use StubKit\Support\Item;
use Symfony\Component\Finder\Finder;

class FieldTest extends TestCase
{
    public $fields;
    public $fieldConfig = [];
    public $mappingConfig = [];

    protected function setUp():void
    {
        $this->fieldConfig = require __DIR__.'/../config/types.php';
        $this->mappingConfig = require __DIR__.'/../config/mappings.php';

        $this->fields = new fields(
            $this->fieldConfig,
            $this->mappingConfig,
        );

        Str::macro('reset', function ($value) {
            return Str::of($value)->snake()->replace('_', ' ');
        });
    }

    public function test_field_string_comma_to_array()
    {
        $this->assertEquals([
            'id', 'name', 'email', 'phone',
        ], $this->fields->extract('id, name, email,phone'));
    }

    public function test_field_string_removes_duplicates()
    {
        $this->assertEquals([
            'id', 'name',
        ], $this->fields->extract('id, id, name'));
    }

    public function test_field_string_removes_trailing_comma()
    {
        $this->assertEquals([
            'id', 'name',
        ], $this->fields->extract('id, name,'));
    }

    public function test_field_string_empty_array_when_empty()
    {
        $this->assertEquals([], $this->fields->extract(''));
    }

    public function test_str_method_returns_stringables_for_comma_list()
    {
        $fields = $this->fields->str('id,name,email');

        $this->assertEquals(['id', 'name', 'email'], $fields);
        $this->assertEquals('ID', $fields[0]->upper());
        $this->assertEquals('Name', $fields[1]->studly());
        $this->assertEquals('mail', $fields[2]->replace('e', ''));
    }

    public function test_default_views_when_no_mapping_found_for_field()
    {
        $this->assertEquals([
            'schema' => 'stubkit::schema.string',
            'faker' => 'stubkit::faker.sentence',
            'create' => 'stubkit::create.text',
            'edit' => 'stubkit::edit.text',
            'rules' => 'stubkit::rules.string',
            'index' => 'stubkit::index.text',
            'show' => 'stubkit::show.text',
        ], $this->fields->views('missing mapping'));
    }

    public function test_field_variation_uses_same_views()
    {
        $this->assertEquals(
            $this->fields->views('fname'),
            $this->fields->views('first_name'),
        );
    }

    public function test_get_single_view_for_specific_type()
    {
        $this->assertEquals(
            'stubkit::schema.string',
            $this->fields->view('random', 'schema')
        );

        $this->assertEquals(
            'stubkit::index.text',
            $this->fields->view('random', 'index')
        );

        $this->assertEquals(
            'stubkit::show.text',
            $this->fields->view('random', 'show')
        );

        $this->assertEquals(
            'stubkit::create.text',
            $this->fields->view('random', 'create')
        );

        $this->assertEquals(
            'stubkit::edit.text',
            $this->fields->view('random', 'edit')
        );

        $this->assertEquals(
            'stubkit::rules.string',
            $this->fields->view('random', 'rules')
        );

        $this->assertEquals(
            'stubkit::faker.sentence',
            $this->fields->view('random', 'faker')
        );
    }

    public function test_at_wild_card_field_mapping()
    {
        $this->assertEquals([
            'schema' => 'stubkit::schema.timestamp',
            'faker' => 'stubkit::faker.datetime',
            'create' => 'stubkit::create.datetime',
            'edit' => 'stubkit::edit.datetime',
            'rules' => 'stubkit::rules.date',
            'index' => 'stubkit::index.timestamp',
            'show' => 'stubkit::show.timestamp',
            'casts' => 'stubkit::casts.datetime',
        ], $this->fields->views('created_at'));
    }

    public function test_id_wild_card_field_mapping()
    {
        $this->assertEquals([
            'schema' => 'stubkit::schema.foreign',
            'faker' => 'stubkit::faker.foreign',
            'create' => 'stubkit::create.number',
            'edit' => 'stubkit::edit.number',
            'rules' => 'stubkit::rules.foreign',
            'index' => 'stubkit::index.text',
            'show' => 'stubkit::show.text',
        ], $this->fields->views('user_id'));
    }

    public function test_is_wild_card_field_mapping()
    {
        $this->assertEquals([
            'schema' => 'stubkit::schema.boolean',
            'faker' => 'stubkit::faker.boolean',
            'create' => 'stubkit::create.checkbox',
            'edit' => 'stubkit::edit.checkbox',
            'rules' => 'stubkit::rules.boolean',
            'index' => 'stubkit::index.text',
            'show' => 'stubkit::show.text',
        ], $this->fields->views('is_active'));
    }

    public function test_date_wild_card_field_mapping()
    {
        $this->assertEquals([
            'schema' => 'stubkit::schema.date',
            'faker' => 'stubkit::faker.date',
            'create' => 'stubkit::create.date',
            'edit' => 'stubkit::edit.date',
            'rules' => 'stubkit::rules.date',
            'index' => 'stubkit::index.text',
            'show' => 'stubkit::show.text',
        ], $this->fields->views('start_date'));
    }

    public function test_time_wild_card_field_mapping()
    {
        $this->assertEquals([
            'schema' => 'stubkit::schema.time',
            'faker' => 'stubkit::faker.time',
            'create' => 'stubkit::create.time',
            'edit' => 'stubkit::edit.time',
            'rules' => 'stubkit::rules.time',
            'index' => 'stubkit::index.text',
            'show' => 'stubkit::show.text',
        ], $this->fields->views('start_time'));
    }

    public function test_item_class_is_the_field_as_stringable()
    {
        $item = new Item('index', 'First Name', 'stubkit::index.text');

        $this->assertEquals('first_name', $item->replace(' ', '_')->lower());
    }

    public function test_item_class_includes_basic_data_for_views()
    {
        $item = new Item('index', 'created_at', 'stubkit::index.timestamp');
        $this->assertInstanceOf(Item::class, $item->data('field'));
        $this->assertEquals('created_at', $item->data('raw'));
        $this->assertEquals('index', $item->data('field_type'));
        $this->assertEquals('CreatedAt', $item->data('field')->studly());
        $this->assertCount(3, $item->data());
    }

    public function test_every_mapping_has_a_field_type()
    {
        foreach ($this->mappingConfig as $variant => $fieldType) {
            $outcome = in_array($fieldType, array_keys($this->fieldConfig));

            if (! $outcome) {
                throw new Exception("field type: ${fieldType} is undefined.");
            }

            $this->assertTrue($outcome);
        }
    }

    public function test_every_definition_file_exists()
    {
        foreach ($this->fieldConfig as $definition => $files) {
            foreach ($files as $type => $view) {
                $view = Str::of($view)
                    ->replace('stubkit::', '')
                    ->replace('.', DIRECTORY_SEPARATOR);


                if (! file_exists(__DIR__."/../views/${view}.blade.php")) {
                    throw new Exception("${view} doesnt exist. definition: ${definition}");
                }

                $this->assertTrue(file_exists(__DIR__."/../views/${view}.blade.php"));
            }
        }
    }

    public function test_every_nested_view_has_new_line()
    {
        $folders = ['faker', 'index', 'rules', 'create', 'edit', 'schema', 'show'];

        foreach ($folders as $folder) {
            $this->assertTrue(is_dir(__DIR__."/../views/${folder}"));
            foreach ((new Finder())->files()->in(__DIR__."/../views/${folder}") as $file) {
                if (Str::endsWith($file->getRealPath(), 'inputs/base.php')) {
                    continue;
                }

                $content = file_get_contents($file->getRealPath());

                if (Str::endsWith($content, "\n") || $content == '') {
                    $this->assertTrue(true);
                } else {
                    throw new Exception("${folder} / {$file->getFilename()} needs to end with a a new line.\n{$file->getRealPath()}");
                }
            }
        }
    }
}
