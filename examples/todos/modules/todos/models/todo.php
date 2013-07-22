<?php

use Kirby\Toolkit\Model\Database as Model;

class Todo extends Model {

  public function validate() {

    return v($this, array(
      'text' => array('required'),
      'done' => array('in' => array(0,1))
    ));

  } 

}