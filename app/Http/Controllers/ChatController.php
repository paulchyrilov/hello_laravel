<?php namespace App\Http\Controllers;

use App\Message;
use App\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
        $users = User::where('username', '!=', 'admin')->where('id', '!=', Auth::user()->id)->get();

        return view('chat.index', compact('users'));
	}

    public function loadHistory(User $user)
    {
        $messages = Auth::user()->historyWithUser($user->id);

        return $messages;
    }

}
