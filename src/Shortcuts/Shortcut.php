<?php

namespace StubKit\Shortcuts;

abstract class Shortcut
{
    /**
     * The current scaffold;.
     * @var
     */
    public $scaffold;

    /**
     * The values to add to syntax.
     * @var array
     */
    public $values = [];

    /**
     * The fields to add to syntax.
     * @var
     */
    public $fields;

    /**
     * Error to display in console.
     * @var
     */
    public $error;

    /**
     * Make the shortcut.
     *
     * @param string $pivot
     * @param null $fields
     * @return void
     */
    public function make(string $pivot, $fields = null)
    {
        //
    }

    /**
     * Set the scaffold.
     * @param $scaffold
     * @return $this
     */
    public function settings($scaffold)
    {
        $this->scaffold = $scaffold;

        return $this;
    }
}
