<?php

namespace StubKit\Support;

use Illuminate\Support\Str;
use StubKit\Facades\StubKit;

class Item
{
    /**
     * @var
     */
    public $type;

    /**
     * @var
     */
    public $field;

    /**
     * @var
     */
    protected $view;

    /**
     * @var array
     */
    protected $data;

    /**
     * Item constructor.
     * @param $type
     * @param $field
     * @param $view
     */
    public function __construct($type, $field, $view)
    {
        $this->type = $type;

        $this->field = $field;

        $this->view = $view;

        $this->data = [
            'field' => $this,
            'field_type' => $this->type,
            'raw' => $this->field,
        ];
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        return Str::reset($this->field)->{$method}(...$args);
    }

    /**
     * @return mixed
     */
    public function view()
    {
        StubKit::activeField($this->field);

        return $this->view;
    }

    /**
     * @param null $key
     * @return array|mixed
     */
    public function data($key = null)
    {
        if (is_null($key)) {
            return $this->data;
        }

        return $this->data[$key];
    }

    /**
     * @return string
     */
    public function asBladeVariable()
    {
        $field = Str::reset($this->field)->snake();

        return '{{ ${{ model.camel }}->'.$field.' }}';
    }
}
