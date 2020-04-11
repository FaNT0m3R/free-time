<? 
include './core/func.php';
session_start();

/* инициалиация переменных  */
$debug = 0;
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


$secur = true;

$startTab = "#sellbuy"; //buytime, soldtime


if(!isset($_SESSION['user']) || !isset($_SESSION['login']))
{
	$user = 0;
	$login = "";
	$_SESSION['user'] = 0;
	$_SESSION['login'] = "";
}
else
{
	$user = $_SESSION['user'];
	$login = $_SESSION['login'];
}

include './core/stdb.php';

$db->query("set names utf8");

if ($user == 0)
	include ("./core/auth.php"); 

include ("./core/control.php");
	
if ($user == 2)
	include ("./core/adm.php"); 


if ($user == 1)
{ 
$curtime = strtotime(date("Y-m-d H:i:s"));

//убрать старые предложения...
$table = $db->query("SELECT id,timeEnd FROM `order` WHERE login='".$login."';");
if ($table!=false){
	for ($i=0; $i<$table->num_rows; $i++){
		$line = $table->fetch_array();
		$ordtime = strtotime($line['timeEnd']);
		if ($curtime >= $ordtime)
			$db->query("DELETE FROM `order` WHERE id=".$line['id'].";");
	}
}
//...и заказы
$table = $db->query("SELECT id,timeEnd FROM `salles` WHERE login='".$login."';");
if ($table!=false){
	for ($i=0; $i<$table->num_rows; $i++){
		$line = $table->fetch_array();
		$ordtime = strtotime($line['timeEnd']);
		if ($curtime >= $ordtime)
			$db->query("DELETE FROM `salles` WHERE id=".$line['id'].";");
	}
}

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title> Free Time </title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="content">
<div class="header"> Free time </div>
<div class="leftp">
	<div onclick="showBuySell()" class="btn_lmenu"> 	<?echo $login;?>	</div>
	<div onclick="showPurchased()" class="btn_lmenu"> 	Заказанное время</div>
	<div onclick="showSold()" class="btn_lmenu">      	Моё свободное время</div>
	<div onclick="showMess()" class="btn_lmenu">      	Дополнительно</div>
	<div onclick="logout()" class="btn_lmenu">			Выйти </div>
	<img src="imgs/clock.png" width="100%">
</div>
<div class="rightp">
	<?
	if ($startTab == '#sellbuy')
		echo '<div id="sellbuy" style="display: block;">';
	else
		echo '<div id="sellbuy" style="display: none;">';
	?>
	
		<div class="subheader" style="overflow:auto; margin-bottom:10px;">
			<div onclick="showBuy()" class="button btn_sellbuy">  Купить свободное время</div>
			<div onclick="showSell()" class="button btn_sellbuy"> Предложить своё время</div>
		</div>
		<div id="placebuy" style="display: block;">
			<form action="index.php" method="POST">
			Сколько свободного времени вам необходимо.<br>
			<input type="number" name="timeSizeHour">часы <input type="number" name="timeSizeMin">мин<br>
			Когда вам нужно это время?<br>
			<input type="date" name="timeStartD"><input type="time" name="timeStartT"><br>
			Опишите, какую работу или задачу необходимо решить, что бы у вас появилось это свободное время.<br>
			<textarea rows=3 cols=50 name="infos"></textarea><br>
			Способ связаться с вами(телефон, WhatApp, Viber и подобное).<br>
			<textarea rows=2 cols=50 name="cont"></textarea><br>
			<input type='submit'></input>
			</form>
		</div>
		<div id="placesell" style="display: none;">
			<form action="index.php" method="POST">
			Сколько времени вы хотите предложить?<br>
			<input type="number" name="timeSizeHour">часы <input type="number" name="timeSizeMin">мин<br>
			Когда у вас это время есть?<br>
			<input type="date" name="timeStartD"><input type="time" name="timeStartT"><br>
			Опишите, какие виды работ вы можете выполнить, какими знаниями и умениями обладаете.<br>
			<textarea rows=4 cols=50 name="type"></textarea><br>
			Где вы находитесь в это свободное время, есть ли личный транспорт и какой.<br>
			<textarea rows=3 cols=50 name="place"></textarea><br>
			Способ связаться с вами(телефон, WhatApp, Viber и подобное).<br>
			<textarea rows=2 cols=50 name="cont"></textarea><br>
			<input type='submit'></input>
			</form>
		</div>
	</div>
	
	
	
	<?
	if ($startTab == '#buytime')
		echo '<div id="buytime" style="display: block;">';
	else
		echo '<div id="buytime" style="display: none;">';
	?>
		<div>
			<div class="subheader">Заказанное время</div>
			<?
			$table = $db->query("SELECT id,time,timeStart, timeEnd, texts,contacts FROM `order` WHERE login='".$login."';");
			if ($table!=false){
			for ($i=0; $i<$table->num_rows; $i++){
				$line = $table->fetch_array();
				$minut = (int)((strtotime($line['timeEnd']) - strtotime($line['timeStart'])) /60);
				$hour = (int)($minut/60);
				$minut = $minut - $hour*60;
				
				echo '<div class="item" id="o'.$line['id'].'">';
				echo '<span class="messtime">'.$line['time'].'</span><br>';
				echo '<span class="messfreetime">',$line['timeStart'].' - ',$hour,' ',getHourStr($hour),' ';
				if ($minut!=0) 
					echo $minut,' ',getMinutStr($minut);
				echo '</span>'.'<br>';
				echo '<b>Данные: </b>'.$line['texts'].'<br>'.'<b>Контакты: </b>'.$line['contacts'].'<br>';
				echo '<div class="button2" onclick="cancelOrder(\''.$line["id"].'\')">Отменить</div>';	
				echo '</div>';
			}
			}
			?>
		</div>
	</div>
	
	<?
	if ($startTab == '#soldtime')
		echo '<div id="soldtime" style="display: block;">';
	else
		echo '<div id="soldtime" style="display: none;">';
	?>
		<div style="padding-bottom:10px;">
			<div class="subheader">Моё свободное время</div>
			<?
			$table = $db->query("SELECT id,time,timeStart, timeEnd, skill, position,contacts FROM `salles` WHERE login='".$login."';");
			if ($table!=false){
			for ($i=0; $i<$table->num_rows; $i++){
				$line = $table->fetch_array();
				
				$minut = (int)((strtotime($line['timeEnd']) - strtotime($line['timeStart'])) /60);
				$hour = (int)($minut/60);
				$minut = $minut - $hour*60;
				
				echo '<div class="item" id="s'.$line['id'].'">';
				echo '<span class="messtime">'.$line['time'].'</span><br>';
				echo '<span class="messfreetime">',$line['timeStart'].' - ',$hour,' ',getHourStr($hour),' ';
				if ($minut!=0) 
					echo $minut,' ',getMinutStr($minut);
				echo '</span><br>';
				echo '<b>Данные: </b>'.$line['skill'].'<br>';
				echo '<b>Место расположения: </b>'.$line['position'].'<br>';
				echo '<b>Контакты: </b>'.$line['contacts'],'<br>';
				echo '<div class="button2" onclick="deleteSeller(\''.$line['id'].'\')">Отменить</div>';
				echo '</div>';
				
			}
			}
			?>
		</div>
	</div>
	<?
	if ($startTab == '#admmess')
		echo '<div id="admmess" style="display: block;">';
	else
		echo '<div id="admmess" style="display: none;">';
	?>
		<div style="padding-bottom:10px;">
			<div class="subheader">Написать автору проекта</div>
			
			
			<form action="index.php" method="POST">
			<textarea rows=6 cols=50 name="textmess"></textarea><br>
			<input type='submit' value="Отправить" name="message"></input>
			<? if($startTab == '#admmess'){ ?>
				<b>Отправлено</b>
			<?}?>
			</form>
		</div>
		<div class="subheader">Сменить пароль </div>
		<input id="newpass" placeholder="новый пароль"> <button onclick="changePass()"> Сменить </button>
	</div>
	
</div>
</div>

<script>
<?
echo 'var oldplace = "'.$startTab.'";';
?>
var oldplace2 = "#placebuy";
</script>
<script language="javascript" src="js/main.js"> </script>
<script language="javascript" src="js/jquery-3.0.0.js"> </script>
</body>
</html>
<?
}
$db->close();

?>
