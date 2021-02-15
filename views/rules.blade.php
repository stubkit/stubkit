@inject('helper', 'StubKit\Support\Fields')
@foreach($helper->get('rules', $fields) as $field)
@include($field->view(), $field->data())
@endforeach