<article>
  <h1><?php echo html($post->title()) ?></h1>
  <p><?php echo html($post->text()) ?></p>
  
  <nav>
    <ul>
      <li><a href="<?php echo app::url('posts > posts::delete', array('id' => $post->id)) ?>">Delete this article</a></li>
      <li><a href="<?php echo app::url() ?>">Back</a></li>
    </ul>
  </nav>

  <section>
    <h1>Comments <?php echo $post->comments()->count() ?></h1>
    <?php foreach($post->comments() as $comment): ?>
    <article id="comment-<?php echo $comment->id() ?>">
      <?php echo html($comment->text()) ?>
    </article>
    <?php endforeach ?>
  </section>
  
  <section>
    <h1>Your comment</h1>
    <?php echo $form ?>
  </section>

</article>