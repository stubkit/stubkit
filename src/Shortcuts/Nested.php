<?php

namespace StubKit\Shortcuts;

use Illuminate\Support\Str;

class Nested extends Shortcut
{
    /**
     * The scaffold config type.
     * @var string
     */
    public $type = 'nested';

    /**
     * Make the shortcut.
     *
     * @param string $nested
     * @param null $fields
     */
    public function make(string $nested, $fields = null)
    {
        $this->values = [
            'model' => $this->scaffold,
            'parent' => $nested,
        ];

        $this->fields = Str::reset($nested)->snake().'_id';

        if ($fields) {
            $this->fields = $fields.', '.$this->fields;
        }
    }
}
