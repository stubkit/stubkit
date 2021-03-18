@php
    if(isset($value) && $value == 'old.create') {
        $value = stubkit("{{ old('{{ field.snake }}') }}", get_defined_vars());
    }
    if(isset($value) && $value == 'old.edit') {
        $value = stubkit("{{ old('{{ field.snake }}', \${{ model.camel }}->{{ field.snake }}) }}", get_defined_vars());
    }
@endphp
<div>
    <label for="{{ $field->snake() }}">
        {{ $field->title() }}
    </label>

    <textarea name="{{ $field->snake() }}" {!! $helper->bind($attributes) !!}>{!! $value !!}</textarea>

    @php echo "@error('{$field->snake()}')" @endphp

    <div style="color: red;">
        @{{ $message }}
    </div>
    @php echo '@enderror'; @endphp

</div>
