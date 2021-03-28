@php

use StubKit\Facades\StubKit;
use StubKit\Support\Fields;

$output = '';

$model = StubKit::syntax()->get('model.snake');

foreach ((new Fields(config('stubkit-types'), config('stubkit-mappings')))->str($fields) as $field) {
    $output .= "'{$field->snake()}': this.$model.{$field->snake()},\n";
}

$output = rtrim($output);

@endphp
{!! $output !!}
