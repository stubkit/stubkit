<?php

namespace StubKit\Support;

use Illuminate\Support\Arr;

class Syntax
{
    /**
     * The compiled variables.
     *
     * @var array
     */
    protected $variables = [];

    /**
     * Search and replace variables.
     *
     * @param string $content
     * @return string
     */
    public function parse(string $content)
    {
        foreach ($this->variables as $search => $replace) {
            $this->variables[$search] = $this->renderValue($replace);
            $value = $this->variables[$search]['rendered'];
            $content = preg_replace_callback("/\s*{{\s*${search}\s*}}/", function ($matches) use ($value) {
                return $this->renderSpacing($value, $matches);
            }, $content);
        }

        return $content;
    }

    /**
     * @return array
     */
    public function renderValue($replace):array
    {
        if(isset($replace['rendered']) && !is_null($replace['rendered'])) {
            return $replace;
        }

        if (! isset($replace['callback'])) {
            $replace = [
                'value' => $replace,
                'callback' => ''
            ];
        }

        if(is_array($replace['value'])) {
            return ['rendered' => null]; // no support for --type=index --type=show
        }

        if (is_callable($replace['callback'])) {
            $replace['rendered'] = $replace['callback']($replace['value']);
        } else {
            $replace['rendered'] = $replace['value'];
        }

        return $replace;
    }

    /**
     * @return string
     */
    public function renderSpacing($value, $matches)
    {
        $lines = explode("\n", $value);

        preg_match('/(\s*){{/', $matches[0], $space);

        if (count($lines) == 1) {
            return $space[1].implode('', $lines);
        }

        foreach ($lines as $index => $line) {
            $lines[$index] = $space[1].$line;
        }

        return rtrim(implode('', $lines));
    }

    /**
     * Compile the variables.
     *
     * @param array $values
     * @param array $globals
     * @param array $variables
     * @return Syntax;
     */
    public function make(array $values = [], array $globals = [], array $variables = [])
    {
        $values = array_filter($values, function ($value) {
            return ! is_bool($value) && $value != '';
        });

        $emptyCallback = function ($value = null) {
            return $value;
        };

        foreach ($variables as $key => $value) {
            $variable = array_key_exists($key, $values) ? $values[$key] : '';

            $this->variables[$key] = [
                'callback' => $emptyCallback,
                'rendered' => null,
                'value' => $variable
            ];

            if (is_array($value)) {
                foreach ($value as $child => $callback) {
                    $this->variables["${key}.${child}"] = [
                        'callback' => $callback,
                        'rendered' => null,
                        'value' => $variable,
                    ];
                }
            } else {
                if (is_callable($value)) {
                    $this->variables[$key] = [
                        'callback' => $value,
                        'rendered' => null,
                        'value' => $variable,
                    ];
                } else {
                    $this->variables[$key] = [
                        'callback' => $emptyCallback,
                        'rendered' => null,
                        'value' => $value,
                    ];
                }
            }
        }

        foreach ($values as $key => $value) {
            if (isset($this->variables[$key])) {
                continue;
            }

            $this->variables[$key] = [
                'callback' => $emptyCallback,
                'rendered' => null,
                'value' => $value,
            ];

            foreach ($globals as $global => $callback) {
                $this->variables[$key.'.'.$global] = [
                    'callback' => $callback,
                    'rendered' => null,
                    'value' => $value,
                ];
            }
        }

        return $this;
    }

    /**
     * Set syntax variables.
     *
     * @param array $variables
     *
     * @return $this
     */
    public function setVariables(array $variables = [])
    {
        $this->variables = $variables;

        return $this;
    }

    /**
     * get syntax variables.
     *
     * @return array
     */
    public function all()
    {
        return $this->variables;
    }

    /**
     * Get single variable.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key)
    {
        $variable = Arr::get($this->variables, $key, [
            'value' => '',
            'rendered' => null,
            'callback' => function () {
                return null;
            },
        ]);

        if(!is_null($variable['rendered'])) {
            return $variable['rendered'];
        }

        $rendered = $variable['callback']($variable['value']);

        $this->variables[$key]['rendered'] = $rendered;

        return $rendered;
    }

    /**
     * Make variables and merge into current variables.
     *
     * @param array $values
     * @param array $globals
     * @param array $options
     *
     * @return void
     */
    public function mergeMake(array $values, array $globals = [], array $options = [])
    {
        $existing = $this->variables;

        $this->make($values, $globals, $options);

        $this->setVariables(array_merge($existing, $this->variables));
    }
}
