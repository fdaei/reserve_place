<?php

namespace App\Livewire\User\Tickets;

use App\Models\Ticket;
use App\Models\TicketChat;
use Livewire\Component;

class Tickets extends Component
{
    public $id;
    public function render(){
        $ticket=Ticket::find($this->id);
        if (!$ticket){
            abort(404);
        }
        if ($ticket->user_id!=auth()->id()){
            abort(404);
        }
        \App\Models\TicketChat::where("ticket_id",$ticket->id)
            ->where("user_id","!=",auth()->id())->update([
                "seen"=>1
            ]);
        return view('livewire.user.tickets.tickets',[
            "ticket"=>$ticket
        ])
            ->extends('app')
            ->section('content');
    }
    public $message;
    public function save(){
        if(!auth()->check()){
            return;
        }
        $this->validate([
            'message' => 'min:3|required|string',
        ], [], [
            'message' => 'پیام',
        ]);
        Ticket::find($this->id)->update([
            "status"=>0,
        ]);
        TicketChat::create([
            "ticket_id"=>$this->id,
            "message"=>$this->message,
            "seen"=>0,
            "user_id" =>auth()->id()
        ]);
        $this->message="";
        $this->dispatch("create");
    }
}
