<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Laracasts\Flash\Flash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $messages = Message::query()->with(['userFrom', 'userTo'])->orderBy('created_at', 'DESC')->paginate(15);

        return view('admin.index', compact('messages'));
    }

    public function deleteMessage(Request $request, Message $message)
    {
        $result = $message->delete();
        if($result) {
            if($request->isXmlHttpRequest()) {
                return ['success' => true];
            }
            Flash::info('Message ' . $message->id . ' removed.');
            return redirect()->back();
        }
        if($request->isXmlHttpRequest()) {
            return ['success' => false, 'message' => 'Can\'t remove message ' . $message->id];
        }
        return redirect()->back()->withErrors(['Can\'t remove message ' . $message->id]);

    }
}
