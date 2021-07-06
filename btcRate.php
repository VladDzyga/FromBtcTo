<?php

	session_start();
	if(!isset($_SESSION['CurUser'])){//Якщо користувач неавторизований, повертаємося на початкову
		header("Location: /");
	}

	$resourceUrl = 'https://bitpay.com/rates/BTC';//Сервіс, що надає інформацію про курс BTC
	$resultData = json_decode(file_get_contents($resourceUrl), TRUE);//Перетворює строку в PHP-значення
	$rates = array("EUR", "GBP", "JPY", "UAH", "USD"); //Список валют
	foreach($resultData['data'] as $oneRate){
		if(in_array($oneRate['code'], $rates)){
			$BTCvalues[$oneRate['code']] = $oneRate['rate'];//Створюємо масив з курсом, де ключі - валюты зі списку
		}
	}
	$FormData = $_POST;//Дані з форми
	if(isset($FormData['searchBut'])){//Пошук
		$search = $FormData['searchInp'];
	}
	if(isset($FormData['cancelBut'])){//Скидання
		unset($search);
	}
	if(isset($FormData['change1'])){//Перший режим конвертера
		$_SESSION['mode'] = false;
	}
	if(isset($FormData['change2'])){//Другий режим конвертера
		
		$_SESSION['mode'] = true;
	}
	if(isset($FormData['convert'])){//Конвертування
		if(trim($FormData['ConVal']) != '' && is_numeric($FormData['ConVal'])){
			if($_SESSION['mode']){//Перевірка режиму
				$result = $FormData['ConVal'] . " BTC = " . $FormData['ConVal']*$BTCvalues[$FormData['list']] . " " . $FormData['list'];
			}
			else{
				$result = $FormData['ConVal'] . " " . $FormData['list'] . " = " . number_format($FormData['ConVal']/$BTCvalues[$FormData['list']], 8, '.', ',') . " BTC";
			}
			if($_SESSION['history'][0] != $result)//Запис в масив історії
				for($i = count($_SESSION['history']); $i > 0 ; $i--){
					$_SESSION['history'][$i] = $_SESSION['history'][$i-1];
				}
				unset($_SESSION['history'][10]);
				$_SESSION['history'][0] = $result;
		}
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
		<div id="header_h2">
			<h2 class="header_title">Тут можна дізнатись курс біткоіну в гривнях</h2>
			<h2 class="header_title">Вітаємо, <?php echo @substr($_SESSION['CurUser'], 0, strpos($_SESSION['CurUser'], "@")); ?>!</h2> <!-- Виводимо ім'я користувача на єкран, без @* -->
		</div>
		<p class="exit">
			<a href="user/logout"><img src="img/buttonOff.png" width="100%"></a>
		</p>
	</div>

	<div id="panel">
		<form id="conv" action="btcRate" method="POST">
			<h2>Конвертер BTC</h2>
				<div>
					<p class="conv_field"><?php
					if($_SESSION['mode']){//Режим BTC --> Валюта
						echo @"<input class='numInp' type='text' name='ConVal'> BTC <button type='submit' name='change1'>&#8660;</button> ";//Поле вводду и кнопка зміни режиму 
  						echo @"<select name='list'>";//Створення списку валют
  						foreach ($rates as $name) {
  							if($FormData['list'] == $name){
  								echo @"<option selected>" . $name . "</option>";
  							}
							else{
								echo @"<option>" . $name . "</option>"; 				
							}		
  						}
  						echo @"</select>";
  					}
  					else{//Режим Валюта --> BTC
  						echo @"<input class='numInp' type='text' name='ConVal'> ";//Поле вводу
  						echo @"<select name='list'>";//Створення списку валют
  						foreach ($rates as $name) {
  							if($FormData['list'] == $name){
  								echo @"<option selected>" . $name . "</option>";
  							}
							else{
								echo @"<option>" . $name . "</option>"; 				
							}		
  						}
  						echo @"</select>";
  						echo @" <button type='submit' name='change2'>&#8660;</button> BTC";//Кнопка зміни режиму
  					}
  					?> <button type="submit" name="convert">Конвертувати</button></p>			
				</div>
			<hr>
			<ul class="listField">
				<?php
					if(isset($_SESSION['history'])){//Виведення історії
						foreach($_SESSION['history'] as $str){
							echo @"<li>" . $str . "</li>";
						}
					}
				 ?>
			</ul>
		</form>
		<form id="curr" action="btcRate" method="POST">
			<h2>Курс валют 1 BTC</h2>
			<p class="conv_field">
				<input class="Inp_search" type="text" name="searchInp" maxlength="3" value="<?php echo @$search; ?>">
				<button type="submit" name="searchBut">Пошук</button>
				<button type="submit" name="cancelBut">Скинути</button>
			</p>
			<hr>
			<ul class="listField">
			<?php 
				if(empty($search)){//Виведення всього списку валют
					foreach($rates as $name){
						echo @"<li>". $name . ": " . $BTCvalues[$name] . "</li>"; 
					}
				}
				else{//Виведення за пошуком
					$find = false;
					foreach($rates as $name){
						if(strpos($name, $search) !== false){
							echo @"<li>". $name . ": " . $BTCvalues[$name] . "</li>";
							$find = true;
						}
					}
					if(!$find){
						echo @"<li>Не знайдено</li>";
					}
				}
			 ?>
			</ul>
		</form>
	</div>
</body>
</html>