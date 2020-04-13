<?php /*
отвечает за диалог авторизации, регистрации и проверку логина-пароля при входе

$_GET[page]=reg/login
$_GET[err]= 
  0 - нет ошибки
  1 - ошибка в сиволах логина
  2 - пользователь уже существует
  3 - пароль содержит запрещённые символы
  4 - email не корректен
  5 - email уже зарегистрирован в системе
*/
const LOGIN = 0;
const REG = 1;

if (!isset($secur))
	if(!$secur)
		exit;
$errlogin = false;
$user = 0; //1 - user, 2 - admin

	/* проверка куков */
if (isset($_COOKIE["var1"]) && isset($_COOKIE["var2"]))
{
	$session = $_COOKIE["var1"];
	$sess2 = $_COOKIE["var2"];
	
	$resl_serv = $db->query("SELECT session, sess2, login, lastdata FROM sess WHERE session='".$session."' AND sess2='".$sess2."';");
	if ($resl_serv == false){
		echo $errSELECT;
		exit();
	}
	
	if ($resl_serv->num_rows==1){
		$sesslog = $resl_serv->fetch_array();
		$login = $sesslog[2];
		$resl_serv = $db->query("SELECT badmin FROM users WHERE login='".$login."';");
		if ($resl_serv->num_rows==1){
			$line = $resl_serv->fetch_array();
			$user = $line[0] + 1;
			$_SESSION['user'] = $user;
			$_SESSION['login'] = $login;
			$db->query("UPDATE `statistics` SET `valuei` = `valuei` + 1 WHERE `id` = 4;");
		}
	} 
	else {
		$user = 0;
	}
	
}

if ((isset($_POST["login"])) && (isset($_POST["password"])))
{
	$login = mysql_escape_sym($_POST["login"]);
	$password = mysql_escape_sym($_POST["password"]);
	
	
	if ((testLogin($login)) && (testPass($password)))
	{
		$password = SHA1($password);
		$rstl = $db->query("SELECT badmin FROM users WHERE login='".$login."' AND pass='".$password."' AND bactiv=1;");
		if ($rstl==false)
			$errlogin = true;
		else
		{
			if($rstl->num_rows == 1)
			{
				$line = $rstl->fetch_array();
				$user = $line[0] + 1;
				$db->query("UPDATE `statistics` SET `valuei` = `valuei` + 1 WHERE `id` = 4;");
				$db->query("UPDATE `users` SET `counter` = `counter` + 1 WHERE login='".$login."';");
				
				
				$currsession = mt_rand(1000,9999999);
				$key2 = mt_rand(1000,9999999);
				$key2 = SHA1($key2);
				setcookie("var1",$currsession, time() + 24*60*60*62);
				setcookie("var2",$key2,time() + 24*60*60*62);
				$sdate = date("Y-m-d H:i:s");
				$db->query("INSERT INTO sess VALUES ('".$currsession."','".$key2."', '".$login."','".$sdate."');");
				$db->query("UPDATE `users` SET `datalast`='".$sdate."' WHERE login='".$login."';");
				$password="";
				$_SESSION['user'] = $user;
				$_SESSION['login'] = $login;
			}
			else
				$errlogin = true;
		}
	} else
		$errlogin = true;
} 


if ($user==0)
{

	
	if (isset($_GET["page"]))
		if ($_GET["page"] == "reg")
			$page = REG;
		else $page = LOGIN;
	else $page = LOGIN;
	
	
	if (isset($_GET["err"]))
		$err = $_GET["err"];
	else
		$err = 0;
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title> Free Times </title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>

<div class="content">
<div class="header"> Free times </div>
<div class="leftp">
	<div class="button btn_lmenu" onclick="showLogin()"> Войти </div>
	<div class="button btn_lmenu" onclick="showRegister()"> Регистрация </div>
	<img src="imgs/clock.jpg" width="100%">
</div>
<div class="rightp">
	<?php
	if($page == REG) {?>
		<div id="flogin" style="display:none;">
	<?php }
	else 
	{?>
		<div id="flogin" style="display:block;">
	<?php } ?>
	 
		На этом сайте вы можете купить или продать своё время
		<form style="font-size:14px;" action="index.php" method="POST">
		<div style="margin:30px; text-align:center;"> 
		<?php 
		if ($errlogin)
			echo '<div style="padding:10px;"> Неверный логин или пароль. </div>';
		?>
		<table style="text-align:center;" >
		<tr>
		    <td> Логин </td>
			<td> Пароль</td>
			<td></td>
		</tr>
		<tr>
			<td> <input type="text" name="login"></td>
		    <td> <input type="password" name="password"></td>
			<td> <input type="submit" value="Вход"></td>
		</tr>
		</table>
		</div>
		</form>
	</div>
	
	<?php	if ($page==REG){?>
	<div id="fregister" style="display:block;">
	<?php }else{ ?>
	<div id="fregister" style="display:none;">
	 <?php } ?>
		<div class="subheader">Регистрация</div>
		<form style="font-size:14px;" action="register.php" method="POST">
		<table style="text-align:center; margin:30px; margin-left:50px;" >
		<tr>
		    <td style="text-align:right;"> Логин </td>
			<td> <input type="text" name="rlogin" onkeypress="testLog()"></td>
			<td style="width:200px; height:40px; font-size:12px; color:#888;">
				<?php if ($err==1)
					echo '<span id="warnlog" style="display:block;">';
				else
					echo '<span id="warnlog" style="display:none;">';
				echo 'допустимы любые буквы, цифры и нижнее подчёркивание</span>';?>
				
				
				<?php if($err==2){
				echo '<span id="warnlog2" style="display:block;">';
				} else {
				echo '<span id="warnlog2" style="display:none;">';
				}
				echo 'этот логин уже зарегистрирован</span>';
				?>
			</td>
		</tr>
		<tr>
		    <td style="text-align:right;"> Пароль</td>
		    <td> <input type="password" name="rpassword" onkeypress="testPas()"></td>
			<td style="width:200px; height:60px; font-size:12px; color:#888;">
				<?php if ($err==3)
					echo '<span id="warnpas" style="display:block;">';
				else
					echo '<span id="warnpas" style="display:none;">';
				echo 'допустимы латинские прописные и строчные символы и цифры</span> ';
				?>
			</td>
		</tr>
		<tr>
		    <td style="text-align:right;"> Повторите пароль</td>
		    <td> <input type="password" onblur="testRepPas()" id="rreppas"></td>
			<td style="font-size:12px; ">  <span id="warnreppas" style="display:none; color:#888;">пароли не совпадают</span> </td>
		</tr>
		<tr>
		    <td style="text-align:right;"> Ваш e-mail</td>
		    <td> <input type="e-mail" name="remail" onkeyup="testEmail()" onfocus="testEmail()"></td>
			<td style="font-size:12px; color:#888;"> 
				<?php if ($err==5)
					echo '<span id="warnemail" style="display:block;">';
				 else
					echo '<span id="warnemail" style="display:none;">';
				echo 'Этот e-mail уже есть в системе</span>';
				
				if($err==4)
					echo '<span id="warnemail2" style="display:block;">';
				else
					echo '<span id="warnemail2" style="display:none;">';
				echo 'Введите ваш действующий e-mail</span>';
				?>
			</td>
		</tr>
		<tr><td></td><td style="text-align:right;"><input type="submit" value="Далее" id="btnsubm" disabled></td></tr>
		</table>
		</form>
	</div>
</div>
</div>

<?php if ($page==REG){?>
<script>
var oldplace3 = "#fregister";
</script>
<?php } else{ ?>
<script>
var oldplace3 = "#flogin";
</script>
<?php }?>
<script language="javascript" src="js/auth.js"></script>
<script language="javascript" src="js/jquery-3.0.0.js"> </script>
</body>
</html>
<?php
exit;
}


function testLogin($logt){
	if (strlen($logt) > 250)
		return false;
	for ($i=0; $i<strlen($logt); $i++)
	{
		if (testchar($logt[$i])==false)
			return false;
	}
	return true;
}

function testPass($pas){
	if (strlen($pas) > 250)
		return false;
	for ($i=0; $i<strlen($pas); $i++)
	{
		if (testchar2($pas[$i])==false)
			return false;
	}
	return true;
}

?>