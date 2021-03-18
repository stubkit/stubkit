@inject('helper', 'StubKit\Support\Fields')
@php
    if(isset($attributes['value']) && $attributes['value'] == 'old.create') {
        $attributes['value'] = stubkit("{{ old('{{ field.snake }}') }}", get_defined_vars());
    }
    if(isset($attributes['value']) && $attributes['value'] == 'old.edit') {
        $attributes['value'] = stubkit("{{ old('{{ field.snake }}', \${{ model.camel }}->{{ field.snake }}) }}", get_defined_vars());
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

