<!DOCTYPE html>
<html lang="es">
<head>
  <title><?php echo TITLE; ?> | Login</title>
  <meta charset="utf-8">
</head>
<body>

<?php echo @$params['tracking']; ?>

<a href="<?php echo $params['url'] . '/' . $pagesList['index']['url']; ?>"> Go back home</a>

<h3 class="margin-bottom-30">Login</h3>

<?php
// Box info
if(!empty($params['box_info']['msg'])) {
    ?>
    <p>
        <?php
        echo $params['box_info']['msg'];
        ?>
    </p>
    <?php
}
?>

<form action="<?php echo $params['url'] . '/' . $pagesList['page_login']['url']; ?>" method="POST">
  <input type="hidden" name="action" value="login" />
  <fieldset>
    <label>Username</label>
    <input type="text" name="username">
  </fieldset>
  <fieldset>
    <label>Password</label>
    <input type="password" name="password">
  </fieldset>
  <fieldset>
    <label><input type="checkbox" name="remember" /> Remember me</label>
  </fieldset>
  <button type="submit">Log in</button>
</form>
</body>
</html>
