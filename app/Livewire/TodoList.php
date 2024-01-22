<?php

namespace App\Livewire;

use App\Models\Todo;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{

    use WithPagination;

    #[Rule('required|min:1|max:100')]
    public $name;

    public $search;

    public $editingID;
    #[Rule('required|min:1|max:100')]
    public $editingName;


    public function create(){

        //validate input
        // create the todo model
        // clear the input
        // send message
        $validated = $this->validateOnly('name');

        Todo::create($validated);

        $this->reset('name');

        session()->flash('success','Saved.');

        $this->resetPage();

    }

    public function delete($id){

        
        Todo::findOrfail($id)->delete();
        
    }

    public function edit($id){

        $this->editingID = $id;
        $this->editingName = Todo::find($id)->name;
    }

    public function toggle($id){

        $todo = Todo::find($id);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function cancel(){

        $this->reset('editingID','editingName');
    }

    public function update($id){

        $this->validateOnly('editingName');

        Todo::find($id)->update([
            'name' => $this->editingName
        ]);

        $this->cancel();
    }

    public function render()
    {

        $todos = Todo::where('name', 'like', "%{$this->search}%")->paginate(5);

        return view('livewire.todo-list',
        [

            'todos' => $todos
        ]);
    }
}
