<?php

use Kirby\Toolkit\Model\Database as Model;

class Comment extends Model {

  static protected $table = 'comments';

  public function validate() {

    return v($this, array(
      'post'  => array('required'),
      'name'  => array('required'), 
      'email' => array('required', 'email'),
      'text'  => array('required'), 
    ));

  }

  protected function insert() {

    $this->added = time();
    
    return parent::insert();

  }

  public function post() {
    return post::find($this->read('post'));
  }

  public function url() {
    return app::url($this->post()->id() . '/#comment-' . $this->id());
  }

}