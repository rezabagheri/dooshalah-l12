<div>
    <h2>Chats</h2>
    @if ($users->isEmpty())
        <p>No users available to chat with.</p>
    @else
        <ul>
            @foreach ($users as $user)
                <li>
                    <a href="{{ route('chat.show', $user->id) }}">{{ $user->display_name }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
