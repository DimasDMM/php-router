<!DOCTYPE html>
<html lang="es">
<head>
  <title><?php echo TITLE; ?> | Error <?php echo $params['num_error']; ?></title>
  <meta charset="utf-8">
  <meta name="robots" content="noindex, nofollow">
</head>
<body>
  <h3><?php echo $params['msg_header']; ?></h3>
  <p><?php echo $params['msg_description']; ?></p>
  <a href="<?php echo $params['url'] . '/' . $pagesList['index']['url']; ?>"> Go back home</a>
</body>
</html>
