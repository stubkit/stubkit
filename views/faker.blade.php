@inject('helper', 'StubKit\Support\Fields')
@foreach($helper->get('faker', $fields) as $field)
@include($field->view(), $field->data())
@endforeach