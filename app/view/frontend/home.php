<!DOCTYPE html>
<html lang="es">
<head>
	<title><?php echo TITLE; ?> | Home</title>
	<meta charset="utf-8">
</head>
<body>
<?php echo @$params['tracking']; ?>

<p>
<?php
if (isset($params['user'])) {
    echo 'Hi ' . $params['user']['name'];
    ?><br><a href="<?php echo $params['url'] . '/' . $pagesList['page_login']['url']; ?>/logout"> Log out</a><?php
} else {
    echo 'You are not logged';
    ?><br><a href="<?php echo $params['url'] . '/' . $pagesList['page_login']['url']; ?>"> Login</a><?php
}
?>
</p>
</body>
</html>
