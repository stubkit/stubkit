<td>{{ $post->id }}</td>
<td>{{ $post->first_name }}</td>
<td>{{ $post->last_name }}</td>
<td title="{{ $post->created_at }}">
    {{ $post->created_at->diffForHumans() }}
</td>
