<?php

namespace StubKit\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Fields
{
    /**
     * @var array
     */
    private $fields;
    /**
     * @var array
     */
    private $mappings;

    /**
     * Construct the fields.
     *
     * @param array $fields
     * @param array $mappings
     */
    public function __construct(array $fields = [], array $mappings = [])
    {
        $this->fields = empty($fields) ? config('stubkit-types') : $fields;

        $this->mappings = empty($mappings) ? config('stubkit-mappings') : $mappings;
    }

    /**
     * Render the fields by type.
     * @param string $type
     * @param string $fields
     * @return string
     */
    public function render(string $type, string $fields)
    {
        $output = '';

        $items = $this->get($type, $fields);

        foreach ($items as $item) {
            $output .= view($item->view())->with($item->data())->render();
        }

        return $output;
    }

    /**
     * Get the include info for field.
     *
     * @param string $type
     * @param string $fields
     *
     * @return array
     */
    public function get(string $type, string $fields)
    {
        $output = [];

        $fields = $this->extract($fields);

        foreach ($fields as $field) {
            $output[] = new Item($type, $field, $this->view($field, $type));
        }

        return $output;
    }

    /**
     * Extract fields string.
     *
     * @param string $string
     *
     * @return array
     */
    public static function extract(string $string)
    {
        $items = explode(',', $string);
        $items = array_map('trim', $items);
        $items = array_unique($items);
        $items = array_filter($items);
        $items = array_values($items);

        return $items;
    }

    /**
     * Convert string to stringables.
     *
     * @param string $fields
     *
     * @return array
     */
    public function str(string $fields)
    {
        $fields = $this->extract($fields);

        foreach ($fields as $index => $field) {
            $fields[$index] = Str::reset($field);
        }

        return $fields;
    }

    /**
     * Get view sets for a field.
     *
     * @param string $field
     *
     * @return array
     */
    public function views(string $field)
    {
        $field = Str::snake($field);

        $selected = Arr::get($this->mappings, $field);

        if (is_null($selected)) {
            $selected = $this->findRegexColumn($field);

            if (is_null($selected)) {
                return $this->fields['default'];
            }
        }

        return Arr::get($this->fields, $selected);
    }

    /**
     * Get single view.
     *
     * @param string $field
     * @param string $type
     *
     * @return string
     */
    public function view(string $field, string $type)
    {
        $mappings = $this->views($field);

        return Arr::get($mappings, $type, 'stubkit::none');
    }

    /**
     * Parse regex for matches.
     *
     * @param string $field
     *
     * @return string
     */
    public function findRegexColumn(string $field)
    {
        foreach ($this->mappings as $regex => $mapping) {
            if (! Str::startsWith($regex, '/')) {
                continue;
            }

            preg_match($regex, $field, $matches);

            if (count($matches)) {
                return $mapping;
            }
        }

        return null;
    }

    /**
     * Add types for fields.
     *
     * @param array $fields
     * @return array
     */
    public function addTypes(array $fields)
    {
        foreach (Arr::get($fields, 'default', []) as $type => $view) {
            $this->fields['default'][$type] = $view;
        }

        foreach (Arr::except($fields, 'default') as $type => $keyValue) {
            $this->fields[$type] = array_merge(
                $this->fields[$type],
                $keyValue
            );
        }

        return $this->fields;
    }

    /**
     * Convert array into html attributes.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function bind($attributes = [])
    {
        $output = '';

        foreach ($attributes as $key => $value) {
            $output .= "${key}=\"${value}\" ";
        }

        return rtrim($output);
    }
}
