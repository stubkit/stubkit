@inject('helper', 'StubKit\Support\Fields')
@foreach($helper->get('edit', $fields) as $field)
@include($field->view(), $field->data())
@endforeach
