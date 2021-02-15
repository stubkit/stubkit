<table>
    <tr>
        <th>Id</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Created At</th>
        <th></th>
    </tr>
    @foreach($posts as $post)
    <tr>
        <td>{{ $post->id }}</td>
        <td>{{ $post->first_name }}</td>
        <td>{{ $post->last_name }}</td>
        <td title="{{ $post->created_at }}">
            {{ $post->created_at->diffForHumans() }}
        </td>
        <td>
            <a href="{{ route('posts.show', $post) }}">
                View
            </a>
        </td>
    </tr>
    @endforeach
</table>