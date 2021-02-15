@inject('helper', 'StubKit\Support\Fields')
<table>
    <tr>
@foreach($helper->str($fields) as $field)
        <th>{{ $field->title() }}</th>
@endforeach
        <th></th>
    </tr>
    @php echo '@foreach(${{ model.camelPlural }} as ${{ model.camel }})'@endphp

    <tr>
@foreach($helper->get('index', $fields) as $field)
        @include($field->view(), $field->data())
@endforeach
        <td>
            <a href="@{{ route('{{ model.slugPlural }}.show', $@{{ model.camel }}) }}">
                View
            </a>
        </td>
    </tr>
    @php echo '@endforeach' @endphp

</table>