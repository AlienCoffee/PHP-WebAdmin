<div id="login_panel_name">Вход в панель управления</div>
<?php
if (isset($_SESSION['flash_message']) && $_SESSION['flash_message']=='error_login'):
	// Забыть сообщение при следующей перезагрузке
	if (!isset($_POST['login_hidden'])) unset($_SESSION['flash_message']);
?>
<div class="login_error">Неправильные имя пользователя или пароль</div>
<?php endif; ?>
<div id="login_box">
	<form action="?act=login" method="post">
		<input type="hidden" name="login_hidden" value="1">
		<div class="label1">Телефон:<input type="text" name="login_user" value=""></div>
		<div class="label1">Пароль:<input type="password" name="password_user"></div>
		<div class="label1 center1"><input type="submit" value="Войти"><div>
	</form>
</div>