<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Hello World!</title>
  </head>
  <style>
  body {
    background-color: #0c0;
  }
  .container {
    margin-top: 45px;
    text-align: center;
  }
  </style>
  <body>
    <div class="container">
      <?= $this->view->content() ?>
    </div>
  </body>
</html>
