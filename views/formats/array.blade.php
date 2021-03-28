@php

$output = '';

$fields = explode(',', $fields);

foreach($fields as $field) {
    $output .= "'" . trim($field) . "',";
}

$output = '['. rtrim($output, ',') .']';

@endphp
{!! $output !!}
