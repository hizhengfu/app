<h1>Posts</h1>
<?php foreach($posts as $post): ?>
<article>
  <h1><a href="<?php echo $post->url() ?>"><?php echo $post->title() ?></a></h1>
  <h4><?php echo $post->comments()->count() ?> comments</h4>
  <p><?php echo html($post->text()) ?></p>
  <a href="<?php echo $post->url() ?>">Read more…</a>
</article>
<?php endforeach ?>

<nav>
  <ul>
    <li><a href="<?php echo app::url('posts > posts::add') ?>">Write a new article</a></li>
  </ul>
</nav>
