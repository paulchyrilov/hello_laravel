<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laravel chat</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="/js/notify.min.js"></script>
</head>
<body>


    @include('common.navbar')

    @if (Session::has('flash_notification'))
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    @include('flash::message')
                </div>
            </div>
        </div>
    @endif


    @if (count($errors) > 0)
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{ $error }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="container-fluid">
	    @yield('content')
    </div>


</body>
</html>
