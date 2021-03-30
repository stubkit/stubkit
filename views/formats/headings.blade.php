@inject('helper', 'StubKit\Support\Fields')
@foreach($helper->str($fields) as $field)
<th>{{ $field->title() }}</th>
@endforeach