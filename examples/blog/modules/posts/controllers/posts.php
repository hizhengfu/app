<?php

use Kirby\Form;

class PostsController extends Controller {

  public function index() {

    $this->layout = new layout('posts > application');
    $this->layout->title = 'Posts';
    $this->layout->content = new view($this);
    $this->layout->content->posts = post::order('id desc')->page(false, 10);

  }

  public function add() {

    $this->layout = new layout('posts > application');
    $this->layout->title = 'Write a new article';    
    $this->layout->content = new view($this);
    $this->layout->content->form = new form(array(
      'title' => array(
        'label'     => 'Title',
        'type'      => 'text', 
        'required'  => true, 
        'autofocus' => true
      ), 
      'text' => array(
        'label'     => 'Text', 
        'type'      => 'textarea', 
        'required'  => true
      )
    ), array(
      'on' => array(
        'submit' => function($form) {

          $post = new post($form->data(array(
            'title', 
            'text'
          )));

          if($post->save()) {
            redirect::home();
          } else {
            $form->raise($post);
          }

        }, 
        'cancel' => function($form) {
          redirect::home();
        }
      )
    ));

  }

  public function show($id) {
    
    $post = $this->post($id);
  
    $this->layout = new layout('posts > application');
    $this->layout->title = $post->title();    
    $this->layout->content = new view($this);
    $this->layout->content->post = $post;
    $this->layout->content->form = new form(array(
      'name' => array(
        'label'     => 'Your name',
        'type'      => 'text', 
        'required'  => true, 
        'autofocus' => true
      ), 
      'email' => array(
        'label'     => 'Your email',
        'type'      => 'email', 
        'required'  => true
      ),
      'text' => array(
        'label'     => 'Your comment',
        'type'      => 'textarea', 
        'required'  => true
      )
    ), array(
      'buttons' => array(
        'submit' => 'Post',
        'cancel' => false
      ), 
      'on' => array(
        'submit' => function($form) use ($post) {

          $comment = new comment($form->data(array(
            'name', 
            'email', 
            'text'
          )));

          $comment->post = $post->id();

          if($comment->save()) {
            redirect::to($comment->url());
          } else {
            $form->raise($comment);
          }

        }
      )
    ));

  }

  public function delete($id) {
    
    $post = $this->post($id);    
    $post->delete();

    redirect::home();

  }

  protected function post($slug) {

    $post = post::where('slug', '=', $slug)->first();
  
    if(!$post) app::raise('not-found', 'The article could not be found');

    return $post;

  }

}