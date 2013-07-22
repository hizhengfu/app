<h1>All Todos</h1>
<ul class="todos">
  <?php foreach($todos as $todo): ?>
  <li>
    <?php echo form::checkbox('todo-' . $todo->id(), $todo->done()) ?>
    <?php echo html($todo->text()) ?>
    <a href="<?php echo app::url() ?>/<?php echo $todo->id() ?>/delete"><small>(delete)</small></a>
  </li>
  <?php endforeach ?>
</ul>

<form method="post" action="<?php echo app::url() ?>/add">
  
  <h2>New todo:</h2>

  <?php echo form::text('todo', '', array('autofocus' => true)) ?>
  <?php echo form::csfr() ?>
  <?php echo form::submit('save', 'Add') ?>

</form>

