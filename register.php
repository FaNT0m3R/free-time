<? 
/*
Отвечает за регистрацию пользователя

если есть rlogin
{
проверить ввод
проверить логин на совпадение
проверить емайл на совпадение
записать в таблицы данные(пароль зашифровать в md5 с отметкой "не активирован" и датой
создать случайный код и отправить на эмейл
добавить в таблицу код и логин
}

если есть rcodereg
{
то убрать "не активирован"
сообщить, что всё ок и можно логиниться
}

если нет ничего, то пересылка на 404

*/

/* инициалиация переменных  */
$debug = 1;
if ($debug!=1){
	$strerrdb = "";
	$strerrreq = "";
	$errSELECT = "";
	$errUPDATE = "";
	$errINSERT = "";
}
else{
	$strerrdb = "Ошибка доступа к базе данных";
	$strerrreq = "Ошибка запроса";
	$errSELECT = "Ошибка выполнения SELECT";
	$errUPDATE = "Ошибка выполнения UPDATE";
	$errINSERT = "Ошибка выполнения INSERT";
}

$site = "www.free-time.by";


$login = "";

/****  создание временного кода и отправка на мыло   ***********************************/
if (isset( $_POST["rlogin"]))
{
	include './core/func.php';
	
	$rlogin = htmlspecialchars($_POST["rlogin"]);
	if (strlen($rlogin) > 250)
	{
		header('Location:http://'.$site.'/index.php?page=reg&err=1');
		exit;
	}
	
	for ($i=0; $i<strlen($rlogin); $i++)
	{
		if (testchar($rlogin[$i])==false)
		{
			header('Location:http://'.$site.'/index.php?page=reg&err=1');
			exit;
		}
	}
	
	$rpassword = htmlspecialchars($_POST["rpassword"]);
	if (strlen($rpassword) > 250)
	{
		header('Location:http://'.$site.'/index.php?page=reg&err=3');
		exit;
	}

	for ($i=0; $i<strlen($rpassword); $i++)
	{
		if (testchar2($rpassword[$i])==false)
		{
			header('Location:http://'.$site.'/index.php?page=reg&err=3');
			exit;
		}
	}

	$remail = htmlspecialchars($_POST["remail"]);
	if (strlen($remail) > 250)
	{
		header('Location:http://'.$site.'e/index.php?page=reg&err=4');
		exit;
	}
	if (!(filter_var($remail,FILTER_VALIDATE_EMAIL)))
	{
		header('Location:http://'.$site.'/index.php?page=reg&err=4');
		exit;
	}

	include './core/stdb.php';
	
	$resl_serv = $db->query("SELECT * FROM users WHERE `login`='".$rlogin."';");
	if ($resl_serv == false)
	{
		echo $errSELECT;
		$db->close();
		exit();
	}
	if ($resl_serv->num_rows!=0)
	{
		header('Location:http://'.$site.'/index.php?page=reg&err=2');
		$db->close();
		exit;
	}

	$resl_serv = $db->query("SELECT * FROM users WHERE email='".$remail."';");
	if ($resl_serv == false)
	{
		echo $errSELECT;
		$db->close();
		exit();
	}
	if ($resl_serv->num_rows!=0)
	{
		header('Location:http://'.$site.'/index.php?page=reg&err=5');
		$db->close();
		exit;
	}
	
	$date = date("Y-m-d H:i:s");
	$passhash = SHA1($rpassword);
	$resl_serv = $db->query("INSERT INTO users VALUES (NULL,'$rlogin','$passhash','$remail',false,false,'$date','$date',0);");
	if ($resl_serv == false)
	{
		echo $errINSERT;
		$db->close();
		exit;
	}
	$regkey = mt_rand(1000000, 9999999);
	$resl_serv = $db->query("INSERT INTO regkeys VALUES ('$regkey', '$rlogin','$date');");
	
	$url = $site.'/register.php?rcodereg='.$regkey;
	$title = 'Регистрация на '.$site;
	$message = 'Для активации Вашего акаунта пройдите по ссылке <a href="'. $url .'">'. $url .'</a>';
	if (!sendMessageMail($remail, 'Регистрация на http://'.$site.' <no-reply@free-time.by>', $title, $message))
		header('Location:http://'.$site.'/index.php?page=reg&err=4');
	$db->close();
	?>
	
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title> Free Times </title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div style="width:500px; margin:0 auto; margin-top:100px;">
На Ваш почтовый ящик отправлено письмо с ссылкой для активации. <br>
Перейдите по ней и регистрация будет завершена.
</div>
</body>
	
	<?
	exit;
}
else
if (isset( $_GET["rcodereg"]))
{
	include './core/stdb.php';
	
	$rcodereg = (int)$_GET["rcodereg"];
	
	$resl_serv = $db->query("SELECT code, login, data FROM `regkeys` WHERE code='".$rcodereg."';");
	if ($resl_serv == false)
	{
		$db->close();
		exit();
	}
	
	if ($resl_serv->num_rows!=1)
	{
		header('Location:http://'.$site.'/index.php');
		$db->close();
		exit;
	}
	
	$lineingo = $resl_serv->fetch_array();
	$rlogin = $lineingo[1];
	
	//всё ок, код совпадает, удаляем код из таблицы кодов, меняем в users bactiv на true
	$resl_serv = $db->query("UPDATE users SET bactiv=true WHERE login='$rlogin' LIMIT 1");
	if ($resl_serv == false)
	{
		$db->close();
		exit();
	}
	
	$resl_serv = $db->query("DELETE FROM regkeys WHERE login='$rlogin'");
	if ($resl_serv == false)
	{
		$db->close();
		exit();
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title> Free Times </title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
Регистрация успешно завершена. Теперь можете перейти 
на <a href="index.php">главную страницу</a> и войти в свой аккаунт.
</body>
<?
	$db->close();
}



?>