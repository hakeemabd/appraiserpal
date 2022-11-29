<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Message;

class CustomerMessageController extends Controller
{
    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
        // $messages=Message::all();
        // return view('messages.messages')->with('messages', $messages);
        return view('messages.messages');
    }
    public function store(Request $request)
    {   
        // dd($request);
        $messages = new Message;
        $messages->sender_id = $request->user()->id;
        $messages->recipient_id = $request->user()->id;
        $messages->message = $request->text;
        $messages->save();
        return "something";
    }
    public function create(){

    }
}
