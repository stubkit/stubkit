<?php

namespace StubKit\Support;

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
            $content = preg_replace_callback("/\s*{{\s*${search}\s*}}/", function ($matches) use ($replace) {

                if (! isset($replace['callback'])) {
                    $replace = ['value' => $replace, 'callback' => ''];
                }

                if (is_callable($replace['callback'])) {
                    $value = $replace['callback']($replace['value']);
                } else {
                    $value = $replace['value'];
                }

                $lines = explode("\n", $value);

                preg_match('/(\s*){{/', $matches[0], $space);

                if (count($lines) == 1) {
                    return $space[1].implode('', $lines);
                }

                foreach ($lines as $index => $line) {
                    $lines[$index] = $space[1].$line;
                }

                return rtrim(implode('', $lines));
            }, $content);

        }

        return $content;
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
        $helper = new Variables($globals, $variables, $this->variables);

        $this->variables = $helper->make($values);

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
