<?php

namespace App\Livewire\User;

use App\Models\Ticket;
use App\Models\TicketChat;
use App\Models\Tour;
use Livewire\Component;

class ContactUs extends Component
{
    public $phone="";
    public $name="";
    public $title="";
    public $area=1;
    public $message="";
    public function mount(){
        $this->area=\App\Models\SupportAreaTickets::where("status",true)->first()->id;
    }
    public function render()
    {
        if(auth()->check()){
           $this->name=auth()->user()->name;
           $this->phone=auth()->user()->phone;
        }
        view()->share('title', "تماس با ما");
        return view('livewire.user.contact-us')
            ->extends("app")
            ->section("content");
    }
    public function save(){
        if(!auth()->check()){
            return;
        }
            $this->validate([
                'title' => 'min:3|required|string',
                'area' => 'min:1|required|integer|exists:support_area_tickets,id',
                'message' => 'min:3|required|string',
            ], [], [
                'title' => 'عنوان',
                'area' => 'بخش',
                'message' => 'پیام',
            ]);
        $ticketId=Ticket::create([
            "title"=>$this->title,
            "area"=>$this->area,
            "status"=>0,
            "user_id" =>auth()->id()
        ]);
        TicketChat::create([
            "ticket_id"=>$ticketId->id,
            "message"=>$this->message,
            "seen"=>0,
            "user_id" =>auth()->id()
        ]);
        $this->area=\App\Models\SupportAreaTickets::where("status",true)->first()->id;
        $this->title="";
        $this->message="";
        $this->dispatch("create");
    }
}
