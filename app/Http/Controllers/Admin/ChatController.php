<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Message;

class ChatController extends Controller
{
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function chatView()
    {
        return view('chat')->with($this->getMessages());
    }

    public function getMessages()
    {
        $messages = $this->message->when(request('id'), function ($q) {
            $q->where("id", "<", request('id'));
        })->orderBy('id','desc')->take(15)->with(['user' => function ($q) {
            $q->select('id', 'name');
        }])->get();
        $count = $this->message->where("id", "<", $messages->last()->id)->count();
        return [
            'messages' => $messages,
            'count' => $count
        ];
    }

    public function loadMore()
    {
        return $this->getMessages();
    }

    public function sendMessage()
    {
        $this->message->user_id = auth()->user()->id;
        $this->message->message_content = request('message_content');
        $this->message->save();
        $message = $this->message->whereId($this->message->id)->with(['user' => function ($q) {
            $q->select('id', 'name');
        }])->first();
        event(new MessageSent($message));
        return $message;
    }
}
