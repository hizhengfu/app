<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo html($title) ?></title>
</head>
<body>
  
  <?php echo $content ?>  

  <script src="http://code.jquery.com/jquery-2.0.3.min.js"></script>
  <script>

    $('.todos li input[type=checkbox]').on('click', function() {

      var $this = $(this);
      var id = $this.attr('name').replace('todo-', '');

      if($this.is(':checked')) {
        var url = './' + id + '/done';
      } else {
        var url = './' + id + '/undone';
      }     

      $.post(url);

    });

  </script>

</body>
</html>