<?php

namespace App\Livewire\Admin;

use App\Models\Province;
use App\Models\Residence;
use App\Models\Ticket;
use App\Models\TicketChat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Message extends Component
{
    use WithPagination;
    public $search="";
    public $id;
    public $ticket;
    public $userPhone;
    public function mount(){
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
        $this->ticket=Ticket::find($this->id);
        $this->area=$this->ticket->area;
        $this->userPhone=User::find($this->ticket->user_id)->phone;
    }

    public function render(){
        $query = \App\Models\TicketChat::query();
        $query->where("ticket_id",$this->id);
        $query->orderBy("id","asc");

        $list = $query->get();
        return view('livewire.admin.message',[
            "list" => $list
        ])
            ->extends("app")
            ->section("content");
    }
    public function updated($propertyName)
    {
        $this->resetPage();
    }


    protected $listeners = ["remove"];
    public $form="empty";
    public $area;
    public $title;
    public $phone;
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
            "area"=>$this->area,
            "status"=>1,
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
    public function remove($id){
        $ticket=Ticket::find($id);
        $ticket=TicketChat::where("ticket_id",$id)->delete();
        $ticket->delete();
        $this->dispatch("removed");
    }

    public function setForm($form,$id=null){
        $this->form=$form;
        if ($form=="edit"){
            $ticket=Ticket::find($id);
            $this->area=$ticket->area;
            $this->phone=$ticket->phone;
        }elseif ($form=="empty"){
            $this->id=null;
            $this->name=null;
        }
    }
    public function add(){
        $this->form="empty";
        Province::create([
            "name"=>$this->name,
            "is_use"=>false
        ]);
        $this->setForm("empty");
        $this->dispatch("create");
    }
}
