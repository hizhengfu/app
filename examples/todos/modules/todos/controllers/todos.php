<?php

class TodosController extends Controller {

  public function index() {

    $this->layout = new layout('todos > application');
    $this->layout->title   = 'Todos';
    $this->layout->content = new view($this);
    $this->layout->content->todos = todo::all();

  }

  public function add() {

    // todo form submit handler
    if(csfr(get('csfr'))) {
      $todo = new todo();
      $todo->text = get('todo');
      $todo->save();
    }

    redirect::back();

  }

  public function done($id) {

    $todo = $this->todo($id);
    $todo->done = 1;
    $todo->save();

    redirect::back();

  }

  public function undone($id) {

    $todo = $this->todo($id);
    $todo->done = 0;
    $todo->save();

    redirect::back();

  }

  public function delete($id) {

    $todo = $this->todo($id);
    $todo->delete();

    redirect::back();

  }

  protected function todo($id) {
    $todo = todo::find($id);
    if(!$todo) raise('The todo could not be found', 404);
    return $todo;
  }

}