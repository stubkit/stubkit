@inject('helper', 'StubKit\Support\Fields')
@foreach($helper->get('create', $fields) as $field)
@include($field->view(), $field->data())
@endforeach