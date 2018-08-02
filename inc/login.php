<div id="login_panel_name">Вход в панель управления</div>
<?php
if (isset($_SESSION['flash_message']) && $_SESSION['flash_message']=='error_login'):
	// Забыть сообщение при следующей перезагрузке
	if (!isset($_POST['port'])) unset($_SESSION['flash_message']);
?>
<div class="login_error">Неправильные имя пользователя или пароль</div>
<?php	
endif;
?>
<div id="login_box">
	<form method="post">
		<input type="hidden" name="port" value="login_port">
		<div class="label1">Телефон:<input type="text" name="login_user"  value=""></div>
		<div class="label1">Пароль:<input type="password" name="password_user"></div>
		<div class="label1 center1"><input type="submit" value="Войти"><div>
	</form>
</div>
