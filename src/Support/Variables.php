<?php

namespace StubKit\Support;

use Illuminate\Support\Arr;

class Variables
{
    public $created = [];

    public $globals = [];

    public $variables = [];

    public $existing = [];

    /**
     * Variables constructor.
     * @param array $globals
     * @param array $variables
     * @param array $existing
     */
    public function __construct(array $globals = [], array $variables = [], array $existing = [])
    {
        $this->globals = $globals;
        $this->variables = $variables;
        $this->existing = $existing;
    }

    /**
     * Make the variables
     * @param $values
     * @return array
     */
    public function make($values): array
    {
        $values = $this->filterValues($values);
        $this->makeVariables($values);
        $this->makeGlobals($values);

        return $this->created;
    }

    /**
     * Only values that have values
     * @param $values
     * @return array
     */
    public function filterValues($values): array
    {
        return array_filter($values, function ($value) {
            return !is_bool($value) && $value != '';
        });
    }

    /**
     * Make the user defined variables.
     * @param array $values
     */
    public function makeVariables(array $values): void
    {
        foreach ($this->variables as $key => $value) {
            $variable = Arr::get($values, $key, '');

            $this->created[$key] = [
                'callback' => is_array($value) ? '' : $value,
                'value' => $variable,
            ];

            if (is_array($value)) {
                foreach ($value as $child => $callback) {
                    $this->created["${key}.${child}"] = [
                        'callback' => $callback,
                        'value' => $variable,
                    ];
                }
            }
        }
    }

    /**
     * Make the * globals.
     * @param array $values
     */
    public function makeGlobals(array $values): void
    {
        foreach ($values as $key => $value) {
            if (isset($this->variables[$key])) {
                continue;
            }

            $this->created[$key] = [
                'callback' => '',
                'value' => $value,
            ];

            foreach ($this->globals as $global => $callback) {
                $this->created["$key.$global"] = [
                    'callback' => $callback,
                    'value' => $value,
                ];
            }
        }
    }
}