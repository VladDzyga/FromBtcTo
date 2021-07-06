<?php
	session_start();
	if(isset($_SESSION['CurUser'])){ //Перевірка, наявності користувача у системі
		header("Location: btcRate");
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Курс біткоіну</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<div id="header">
		<h2 class="header_title">Тут можна дізнатись курс біткоіну в гривнях</h2>
	</div>		
	<?php if(!isset($_SESSION['CurUser'])) : ?> <!-- Якщо користувач відсутній, то виводиться повідомлення для авторизації --> 
		<p class="autho_mess">Для використання сервісу необхідно <a href="user/login">авторизуватися</a></p>
	<?php endif; ?>
</body>
</html>