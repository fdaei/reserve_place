<?php

namespace App\Livewire\Admin;

use App\Models\City;
use Illuminate\Support\Facades\Redirect;
use App\Models\Province;
use App\Models\Residence;
use App\Models\Ticket;
use App\Models\TicketChat;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Tickets extends Component
{
    use WithPagination;
    public $search="";

    public function mount(){
        if (!auth()->check() || auth()->user()->phone !== '09123002501') {
            return Redirect::to("");
        }
    }

    public function render()
    {

        $query = \App\Models\Ticket::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $list = $query->orderBy('id', 'DESC')->paginate(10);
        return view('livewire.admin.tickets',[
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
    public function edit(){
        Auth::logout();
        $user = \App\Models\User::find(Residence::find($this->id)->user_id);
        Auth::login($user);
        return redirect('/edit-residence/'.$this->id);
    }
}
