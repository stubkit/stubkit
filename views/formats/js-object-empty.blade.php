@php

use StubKit\Support\Fields;

$output = '';

foreach ((new Fields(config('stubkit-types'), config('stubkit-mappings')))->str($fields) as $field) {
    $output .= "'{$field->snake()}': '',\n";
}

$output = rtrim($output);

@endphp
{!! $output !!}
