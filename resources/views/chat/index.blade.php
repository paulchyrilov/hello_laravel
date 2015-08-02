@extends('common/layout')

@section('content')

    <script type="text/javascript">
        var user = "<?= Auth::user()->id ?>",
            users = {!! $users->toJson() !!},
            port = "{{ isset($chatPort) ? $chatPort : '9090' }}",
            uri = "<?= explode(':', str_replace('http://', '', str_replace('https://', '', App::make('url')->to('/'))))[0]; ?>";
    </script>
    <script type="text/javascript" src="/js/chat.js"></script>

    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading">Chat</div>

                <div class="panel-body">
                    <div id="chatMessages">
                    </div>
                    <div style="display:table; width: 100%;">
                        <input style="display:table-cell; width: 100%;"type="text" name="chatText" id="chatText" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default">
                <div class="panel-heading">Users</div>

                <div class="panel-body">
                    <ul class="list-group" id="users">
                        @foreach($users as $user)
                            <li id="user_{{ $user->id }}" data-id="{{ $user->id }}" data-history="{{ url('loadHistory', ['user' => $user]) }}" class="list-group-item">{{ $user->username }}<span class="badge hide"></span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
