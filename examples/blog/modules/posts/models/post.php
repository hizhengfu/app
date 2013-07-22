<?php

use Kirby\Toolkit\Model\Database as Model;

class Post extends Model {

  public function validate() {

    return v($this, array(
      'title' => array('required'),
      'text'  => array('required')
    ));

  }

  public function comments() {
    return comment::where('post', '=', $this->id())->all();
  }

  public function url() {
    return app::url($this->slug());
  }

  public function setTitle($title) {

    $this->write('slug', str::slug($title));
    $this->write('title', $title);

  }

  protected function insert() {

    $this->added = time();

    return parent::insert();
  
  }

  public function delete() {
    
    // delete all comments first
    comment::where('post', '=', $this->id())->delete();
    
    // now delete the post
    parent::delete();
  
  }

}