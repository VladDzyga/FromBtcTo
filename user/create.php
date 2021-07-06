<?php
	require '../db.php';
	session_start();
	if(isset($_SESSION['CurUser'])){ //Перевірка, наявності користувача у системі
		header("Location: ../btcRate");
	}


	$FormData = $_POST; //Дані з форми
	$errors = array(); //Масив помилок
	if(isset($FormData['button'])){
		if(trim($FormData['email']) == ''){ 
			$errors[] = "email_error";//Помилка про не введений email
		}
		if(trim($FormData['password']) == ''){
			$errors[] = "pass_error";//Помилка про не введений пароль
		}
		if(trim($FormData['control_pass']) == ''){
			$errors[] = "cont_error";//Помилка про не введений повторний пароль
		}
		if($FormData['control_pass'] != $FormData['password']){
			$errors[] = "same_error";//Помилка пароли не збігаються
		}
		if(R::count('usersdata', "email = ?" , array($FormData['email'])) > 0){
			$errors[] = "already_error";//Помилка про вже зареєстрований email
		}
		if(empty($errors)){//Якщо помилок не має то зберігаємо користувача та повертаємося до початкової сторінки
			$_SESSION['CurUser'] = $FormData['email']; //Записуємо користувача до системи як авторизованого
			$user = R::dispense('usersdata');//Додамо користувача до БД
			$user->email = $FormData['email'];
			$user->password = password_hash($FormData['password'], PASSWORD_DEFAULT);
			R::store($user);
			header("Location: /");
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Реєстрація</title>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body style="display: flex; justify-content: space-around;">
	<form id="login_block" action="create" method="POST">
		<h2>Реєстрація</h2>
		<p>
			<p class="input_title">Ваш Email</p>
			<input type="email" name="email" value="<?php echo @$FormData['email']; ?>"> <!-- Не витираємо email при оновлені сторінки -->
			<?php if(in_array("email_error", $errors)) : ?> <!-- Виводимо помилки в форму, якщо вони є в масиві помилок -->
				<p class="error_text">Введіть Email</p>
			<?php elseif(in_array("already_error", $errors)) : ?>
				<p class="error_text">Email вже зареєстровано</p>
			<?php endif; ?>
		</p>
		<p>
			<p class="input_title">Ваш пароль</p>
			<input type="password" name="password">
			<?php if(in_array("pass_error", $errors)) : ?><!-- Виводимо помилки в форму, якщо вони є в масиві помилок -->
				<p class="error_text">Введіть Пароль</p>
			<?php endif; ?>
		</p>
		<p>
			<p class="input_title">Підтвердіть пароль</p>
			<input type="password" name="control_pass">
			<?php if(in_array("cont_error", $errors)) : ?><!-- Виводимо помилки в форму, якщо вони є в масиві помилок -->
				<p class="error_text">Введіть Пароль ще раз</p>
			<?php elseif(in_array("same_error", $errors)) : ?>
				<p class="error_text">Паролі не співпадають</p>
			<?php endif; ?>
		</p>
		<p>
			<button type="submit" class="form_butt" name="button">Зареєструватися</button>
		</p>
		<p class="reg_mess">Вже маєте аккаунту? - <a href="login">Увійдіть</a></p>
	</form>
</body>
</html>