{!! $messages->render() !!}
<table class="table table-striped">
    <thead>
        <tr>
            <td>Time</td>
            <td>From</td>
            <td>To</td>
            <td>Message</td>
            <td>Actions</td>
        </tr>
    </thead>
    <tbody>
        @foreach($messages as $message)
            <tr>
                <td class="col-md-2">{{ $message->created_at }}</td>
                <td class="col-md-1">{{ $message->userFrom->username }}</td>
                <td class="col-md-1">{{ $message->userTo->username }}</td>
                <td class="col-md-4">{{ $message->text }}</td>
                <td class="col-md-1"><a href="{{ action('AdminController@deleteMessage', $message) }}" class="glyphicon glyphicon-trash"> </a></td>
            </tr>
        @endforeach
    </tbody>
</table>
{!! $messages->render() !!}
