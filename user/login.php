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
		if(empty($errors)){//Якщо помилок не має, то перевіряємо введені дані
			$user = R::findOne('usersdata', "email = ?", array($FormData['email']));
			if(isset($user)){
				if(password_verify($FormData['password'], $user->password)){
					$_SESSION['CurUser'] = $FormData['email'];
					header("Location: /");
				}
				else{
					$errors[] = "notp_error";//Помилка, що пароль не вірний
				}
			}
			else{
				$errors[] = "notf_error";//Помилка, що email не зареєстровано
			}

		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Вхід</title>
	<link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body style="display: flex; justify-content: space-around;">
	<form id="login_block" action="login" method="POST">
		<h2>Вхід</h2>
		<p>
			<p class="input_title">Email</p>
			<input type="email" name="email" value="<?php echo @$FormData['email']; ?>"><!-- Не витираємо email при оновлені сторінки -->
			<?php if(in_array("email_error", $errors)) : ?><!-- Виводимо помилки в форму, якщо вони є в масиві помилок -->
				<p class="error_text">Введіть Email</p>
			<?php elseif(in_array("notf_error", $errors)) : ?>
				<p class="error_text">Email не зареєстровано</p>
			<?php endif; ?>
		</p>
		<p>
			<p class="input_title">Пароль</p>
			<input type="password" name="password">
			<?php if(in_array("pass_error", $errors)) : ?><!-- Виводимо помилки в форму, якщо вони є в масиві помилок -->
				<p class="error_text">Введіть Пароль</p>
			<?php elseif(in_array("notp_error", $errors)) : ?>
				<p class="error_text">Невірний пароль</p>
			<?php endif; ?>
		</p>
		<p>
			<button type="submit" class="form_butt" name="button">Увійти</button>
		</p>
		<p class="reg_mess">Не маєте аккаунту? - <a href="create">Зареєструйтеся</a></p>
	</form>
</body>
</html>