<?php

namespace App\Livewire;

use App\Models\Todo;
use Exception;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TodoList extends Component
{
    use WithPagination;

    #[Rule('required|min:4|max:25')]
    public $name;
    public $search;

    public $editingTodoId;

    #[Rule('required|min:4|max:25')]
    public $editingTodoName;

    public function create(){
      // Validation
      $valited = $this->validateOnly('name');
      //    Create User
       Todo::create($valited);
      //    Clear Inputs
      $this->reset(['name']);
       //  flash Messages
       request()->session()->flash('success' , 'Created.');
       $this->resetPage();
    }

    public function delete($todoId){

        try {
            Todo::findOrfail($todoId)->delete();
        } catch (Exception $e) {
          session()->flash('error' , 'Error to delete todo!');
          return;
        }

    }

    public function toggle($todoId){
        $todo = Todo::find($todoId);
        $todo->completed = !$todo->completed;
        $todo->save();
    }

    public function edit($todoId){
       $this->editingTodoId = $todoId;
       $this->editingTodoName= Todo::find($todoId)->name;

    }

    public function cancalEdit(){
        $this->reset('editingTodoId' ,'editingTodoName');
    }

    public function update(){
        $this->validateOnly('editingTodoName');
        Todo::find($this->editingTodoId)->update(
            [
               'name' => $this->editingTodoName
            ]);
        $this->cancalEdit();
    }
    public function render()
    {

        return view('livewire.todo-list',[
            'todos' => Todo::latest()->where('name' , 'like' ,"%{$this->search}%")->paginate(4)
        ]);
    }
}
