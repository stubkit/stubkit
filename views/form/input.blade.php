@inject('helper', 'StubKit\Support\Fields')
@php
    if($field_type == 'create') {
        $attributes['value'] = stubkit("{{ old('{{ field.snake }}') }}", get_defined_vars());
    }
    if($field_type == 'edit') {
        $attributes['value'] = stubkit("{{ old('{{ field.snake }}', \${{ model.camel }}->{{ field.snake }}) }}", get_defined_vars());
    }

    if(isset($attributes['type']) && $attributes['type'] == 'file') {
        unset($attributes['value']);
    }
@endphp
<div>
    <label for="{{ $field->snake() }}">
        {{ $field->title() }}
    </label>

    <input name="{{ $field->snake() }}" {!! $helper->bind($attributes) !!} />

    @php echo "@error('{$field->snake()}')" @endphp

    <div style="color: red;">
        @{{ $message }}
    </div>
    @php echo '@enderror'; @endphp

</div>

