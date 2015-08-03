@extends('common.layout')

@section('content')
    <div class="panel panel-default">
        <div class="panel-heading">
            {{ $messages->perPage() }} items per page. Total : {{$messages->total()}}.
        </div>
        <div class="panel-body">
            @include('admin.messages_listing')
        </div>
    </div>
@endsection