<?php
/*выбор продавцов*/

/*if (!isset($_POST["idorder"]))
	exit;
*/


session_start();
$user = $_SESSION['user'];
$login = $_SESSION['login'];

if ($user!=2)
	exit;


$name_db = "user2031337_freetime";
$login_db = "freetimer";
$passwd_db = "rPtTasdh355";

$db = mysqli_connect("localhost",$login_db,$passwd_db,$name_db);
if ($db->connect_errno) {
	echo $strerrdb;
	exit();
} 	
$db->query("set names utf8");

/********    страница, выполняющая назначение. выодит статус операции и ссылку на главную   *************************************/
if (isset($_GET['selsel']))
{
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title> Free Time </title>
<link href="../css/main.css" rel="stylesheet" type="text/css">
</head>
<body>

<?php
	$ordtable = $db->query("SELECT timeStart,timeEnd,state FROM `order` WHERE id=".$_GET['ordid'].";");
	if ($ordtable==false){
		$db->close();exit;}
	$ord = $ordtable->fetch_array();
	
	//освобождаем всех, кто работает над задачей. удаляем из exchange инфу с этим заданием.
	//	Потом в цикле добавим снова, но уже актуальное
	
	$salltable = $db->query("SELECT sallesid FROM `exchange` WHERE orderid=".$_GET['ordid'].";");
	for ($i=0; $i<$salltable->num_rows; $i++)
	{ 
		$line = $salltable->fetch_array();
		$db->query("UPDATE `salles` SET state=0 WHERE id=".$line['sallesid'].";");
	}
	$db->query("DELETE FROM `exchange` WHERE orderid=".$_GET['ordid'].";");
	$db->query("UPDATE `orders` SET state=0 WHERE id=".$_GET['ordid'].";");
	foreach ($_GET as $key => $value)
	{ 
		if ($value== "on")
		{
			$sallestable = $db->query("SELECT timeStart,timeEnd,time,login,state FROM `salles` WHERE id=".$key.";");
			if ($sallestable==false){
				$db->close();exit;}	
			$salles = $sallestable->fetch_array();
			/*
			проверить state прродавца и заказа
			сравнить время продавца с временем заказa
			установить state для продавца $key. 
			установить state для заказа id. 
			*/
			
			if ($salles['state']==2)
			{
				echo $salles['login'],' уже занят.<br>';
				echo '<a href="../index.php">Главная</a><br>';
				$db->close();
				exit;
			}
			
			if (!testTime($salles['timeStart'],$salles['timeEnd'],$ord['timeStart'],$ord['timeEnd']))
			{
				echo 'Предложенное свободное время не входит в интервал:',
					$salles['time'],' ',$salles['login'],': ',$salles['timeStart'],' - ',$salles['timeEnd'],'<br>';
				echo '<a href="../index.php">Главная</a><br>';
				$db->close();
				exit;
			}
			
			$fulltime = false;
			$state = 0;
			if (insideTime($salles['timeStart'],$salles['timeEnd'],$ord['timeStart'],$ord['timeEnd'])){
				$state = 2;
			}
			else
				$state = 1;
			$db->query("UPDATE `salles` SET state=".$state." WHERE id=".$key.";");
			
			if (insideTime($ord['timeStart'],$ord['timeEnd'],$salles['timeStart'],$salles['timeEnd'])){			
				$state=2;
				$fulltime = true;
			}
			else
				$state=1;
			$db->query("UPDATE `orders` SET state=".$state." WHERE id=".$_GET['ordid'].";");
			
			//заносим в текущие задачи
			$db->query("INSERT INTO `exchange` (`timeStart`,`timeEnd`,`sallesid`,`orderid`)
							VALUES ('".$ord['timeStart']."', '".$ord['timeEnd']."', ".$key.", ".$_GET['ordid'].");");
			
			echo 'Продавец успешно назначен.<br>';
			if (!$fulltime)
				echo 'Запрашиваемое время ещё не полностью разделено между продавцами.<br>';
		}
	}
	echo '<a href="../index.php">Главная</a><br>';
	$db->close();
?>
</body>
</html>

<?php
	exit;
}
/**************  основной диалог  ************************************************************/
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title> Free Time </title>
<link href="../css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="content">
<div class="header"> Free time </div>
<div style="border:1px solid black; margin: 10px;">
<?php
	$order = $db->query("SELECT login,time,timeStart,timeEnd,texts,contacts,comments FROM `order` WHERE id=".$_GET["idorder"].";");
	if ($order!=false)
	{
		$line = $order->fetch_array();
		echo '<b>',$line['time'],':</b> ',$line['timeStart'],' - ',$line['timeEnd'],'<br>',$line['texts'],'<br>',$line['contacts'];
	}
	
?>
</div>

<form action="sels.php">
<div >
<?php
	include 'func.php';
	
	if (!isset($_POST['all_sellers']))
	{	
		$tSt = $line['timeStart'];
		$tEn = $line['timeEnd'];
		$table = $db->query("SELECT id, login, time, timeStart, timeEnd,skill,position,contacts FROM `salles` WHERE 
			NOT(((timeStart <'".$tSt."') AND (timeEnd<='".$tSt."')) OR
				((timeStart >='".$tEn."') AND (timeEnd>'".$tEn."')));");
	}
	else
	{
		$table = $db->query("SELECT id, login, time, timeStart, timeEnd FROM `salles`;");
	}
	
//сделаем массив с теми, кто уже работает с этим временем
	$oldsell = $db->query("SELECT id, timeStart, timeEnd,sallesid FROM `exchange` WHERE orderid=".$_GET["idorder"].";");
	
	if ($oldsell==false)
		exit;
	for ($i=0; $i<$table->num_rows; $i++){
		$line = $oldsell->fetch_array();
		$sallescon[$line['sallesid']]=true;
	}
/////////////////////////////////////////////////////////	
	
	if ($table!=false){
	for ($i=0; $i<$table->num_rows; $i++){
		/*$line = $table->fetch_array();
		echo '<b>',$line['time'],'</b>:<br>',$line['login'],': ',$line['timeStart'],'  ',$line['timeEnd'],'<br>';
		*/
		
		$line = $table->fetch_array();
		
		$minut = (int)((strtotime($line['timeEnd']) - strtotime($line['timeStart'])) /60);
		$hour = (int)($minut/60);
		$minut = $minut - $hour*60;
		
		echo '<span class="messtime">'.$line['time'].'</span><br>';
		echo '<span class="messfreetime">',$line['timeStart'].' - ',$hour,' ',getHourStr($hour),' ';
		if ($minut!=0) 
			echo $minut,' ',getMinutStr($minut);
		echo '</span>'.'<br>';
		echo $line['skill'].'<br>'.$line['position'].'<br>'.$line['contacts'].'<br>';
		if (isset($sallescon[$line['id']]))
			echo '<input type="checkbox" checked name="'.$line['id'].'">выбрать<br>';
		else
			echo '<input type="checkbox" name="'.$line['id'].'">выбрать<br>';
		echo '<script> items.push('.$line['id'].'); </script>';
		echo '<hr>';
	}
	}
?>

</div>
<?php
echo '<input type="hidden" name="ordid" value="'.$_GET['idorder'].'">';
?>
<input type="submit" value="Продолжить" name='selsel'>
</div>
</form>

</body>
</html>





<?php
$db->close();


function testTime($tSt1,$tEn1,$tSt2,$tEn2)
{
	if ((($tSt1<$tSt2) and ($tEn1<$tSt2)) or
		(($tSt1>$tEn2) and ($tEn1>$tEn2)))
		return false;
	return true;
}

function insideTime($tSt1,$tEn1,$tSt2,$tEn2)
{
	if (($tSt1<$tSt2) or ($tEn1>$tEn2))
		return false;
	return true;
}


?>


