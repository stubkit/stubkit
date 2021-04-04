<?php

namespace StubKit\Shortcuts;

use Illuminate\Support\Str;

class Pivot extends Shortcut
{
    /**
     * The scaffold config type.
     * @var string
     */
    public $type = 'pivot';

    /**
     * Make the shortcut.
     *
     * @param string $pivot
     * @param null $fields
     */
    public function make(string $pivot, $fields = null)
    {
        $pivot = array_map('trim', explode(',', $pivot));

        if (count($pivot) !== 2) {
            $this->error = 'Pivots require two comma separated models.';

            return;
        }

        $this->values = [
            'model' => $this->scaffold,
            'parent' => $pivot[0],
            'child' => $pivot[1],
        ];

        $this->fields = $this->addFields($fields);
    }

    /**
     * Add extra fields to existing.
     * @param string|null $fields
     * @return string
     */
    public function addFields($fields = null)
    {
        $columns = Str::reset($this->values['parent'])->snake().'_id, ';
        $columns .= Str::reset($this->values['child'])->snake().'_id';

        if ($fields) {
            $columns = $columns.', '.$fields;
        }

        return $columns;
    }
}
