@inject('helper', 'StubKit\Support\Fields')
@foreach($helper->get('schema', $fields) as $field)
@include($field->view(), $field->data())
@endforeach