@inject('helper', 'StubKit\Support\Fields')
<table>
@foreach($helper->get('show', $fields) as $field)
    @include($field->view(), $field->data())
@endforeach
</table>
