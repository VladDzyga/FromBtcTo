<?php
	session_start();
	unset($_SESSION['CurUser']);//Видаляємо авторизованого користувача
	unset($_SESSION['history']);//Видаляємо історію користувача
	unset($_SESSION['mode']);//Видаляємо режим конвертера
	header("Location: /");//Повертаємося до початкової сторінки
?>

