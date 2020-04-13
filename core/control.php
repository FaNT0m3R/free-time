<?php

if ($secur != true)
	exit;

if (isset($_GET['query']))
	$query = $_GET['query'];
else
	$query = "";


if (isset($_GET['id']))
	$id = $_GET['id'];
else
	$id = "";

if (isset($_GET['newpass']))
	$newpass = $_GET['newpass'];
else
	$newpass = "";

/************************************************************************/
/**  admin **************************************************************/
/************************************************************************/
if ($user == 2) 
{




if ($query=='cancelOrder')    //ajax
{	/*удалить запись order-заказ времени*/
	$id = intval($id);
	$db->query("DELETE FROM `order` WHERE id=".$id.";");
	exit;
}

	
if ($query=='removeMess')    //ajax
{
	$id = intval($id);
	/*удалить сообщение*/
	$db->query("DELETE FROM `admmess` WHERE id=".$id.";");
	exit;
}


if ($query=='setPass')		//ajax
{
	$rpass = $newpass;
	for ($i=0; $i<strlen($rpass); $i++)
	{
		if (testchar2($rpass[$i])==false)
		{
			echo 'error';
			exit;
		}
	}
	
	$passhash = SHA1($rpass);
	$db->query("UPDATE `users` SET pass='".$passhash."' WHERE login='".$login."';");
	exit;
}


if ($query=='setUserPass')		//ajax
{	
	$rpass = $newpass;
	for ($i=0; $i<strlen($rpass); $i++)
	{
		if (testchar2($rpass[$i])==false)
		{
			echo 'error';
			exit;
		}
	}
	$passhash = SHA1($rpass);
	$db->query("UPDATE `users` SET pass='".$passhash."' WHERE id='".$id."';");
	exit;
}	

if ($query=='delUser')		//ajax
{
	$id = intval($id);
	$db->query("DELETE FROM `users` WHERE id='".$id."';");
	exit;
}

if ($query=='clearDB')		//ajax
{
//удалить старые записи регистрации
//V удалить устаревшие заказы и предложения
//V удалить неверные записи в exchanges 
//V удалить старые сессии


/*
//удалить неверные записи в exchanges
получаем всю таблице exch.. . проверяем каждый id на существование. Если кого-то нет, то стираем запись из exch..
*/
	$exchTabl = $db->query("SELECT id,sallesid,orderid FROM `exchange`;");
	if ($exchTabl==false)
		exit;
	for ($i=0; $i<$exchTabl->num_rows; $i++)
	{
		$bdel = false;
		$line = $exchTabl->fetch_array();
		$testTabl = $db->query("SELECT id FROM `salles` WHERE id='".$line['sallesid']."';");
		if ($testTabl->num_rows > 0) $bdel = true;
		$testTabl = $db->query("SELECT id FROM `orders` WHERE id='".$line['orderid']."';");
		if ($testTabl->num_rows > 0) $bdel = true;
		if ($bdel){
			$db->query("DELETE FROM `exchange` WHERE id='".$line['id']."';");
			$db->query("UPDATE `salles` SET state='0' WHERE id='".$line['sallesid']."';");	//пишем, что свободен(хотя надо бы поправить)
			$db->query("UPDATE `orders` SET state='0' WHERE id='".$line['orderid']."';"); 	//пишем, что свободен(хотя надо бы поправить)
		}
	}
	
	
	
	
	
	$curtime = strtotime(date("Y-m-d H:i:s"));
	//убрать старые предложения...
	$table = $db->query("SELECT id,timeEnd FROM `orders`;");
	if ($table!=false){
		for ($i=0; $i<$table->num_rows; $i++){
			$line = $table->fetch_array();
			$ordtime = strtotime($line['timeEnd']);
			if ($curtime >= $ordtime)
				$db->query("DELETE FROM `orders` WHERE id=".$line['id'].";");
		}
	}
	//...и заказы
	$table = $db->query("SELECT id,timeEnd FROM `salles`;");
	if ($table!=false){
		for ($i=0; $i<$table->num_rows; $i++){
			$line = $table->fetch_array();
			$ordtime = strtotime($line['timeEnd']);
			if ($curtime >= $ordtime)
				$db->query("DELETE FROM `salles` WHERE id=".$line['id'].";");
		}
	}
	$table.close();
	
	//сносим сессии, которым больше 3 месяцев
	$table = $db->query("SELECT session,lastdata FROM `sess`;");
	if ($table!=false){
		for ($i=0; $i<$table->num_rows; $i++){
			$line = $table->fetch_array();
			$sestime = strtotime($line['lastdata']);
			if ($curtime-$sestime > 3*30*24*3600)
				$db->query("DELETE FROM `sess` WHERE session=".$line['session'].";");
		}
	}
	$table.close();
	
	//сносим старые регистрации
	$table = $db->query("SELECT session,lastdata FROM `regkey`;");
	if ($table!=false){
		for ($i=0; $i<$table->num_rows; $i++){
			$line = $table->fetch_array();
			$sestime = strtotime($line['lastdata']);
			if ($curtime-$sestime > 3*30*24*3600)
				$db->query("DELETE FROM `sess` WHERE session=".$line['session'].";");
		}
	}
	$table.close();
	$db.close();
	exit;
}

}

/***********************************************************************/
/**  user **************************************************************/
/***********************************************************************/

if ($user == 1)
{

if (isset($_POST['infos']))    //not ajax
{
	/*запрос времени */
	$queryStr = "INSERT INTO `orders`(`login`, `time`, `timeStart`, `timeEnd`, `texts`, `contacts`) VALUES (";
	$sdate = date("Y-m-d H:i:s");
	$queryStr = $queryStr."'".$login."','".$sdate."', ";
	
	if ((testCorrectDate($_POST['timeStartD'])) &&
		(testCorrectTime($_POST['timeStartT'])))
		$queryStr = $queryStr."'".$_POST['timeStartD']." ".$_POST['timeStartT']."', ";
	else
	{
		$err='time';
		include 'errdo.php';
		exit;
	}

	if ((testCorrectInt($_POST['timeSizeHour'])) &&
		(testCorrectInt($_POST['timeSizeMin'])))
	{
		$unitimes = strtotime($_POST['timeStartD']." ".$_POST['timeStartT']);
		$unitime = strtotime($unitimes);
		$unitime = $unitimes+(((int)$_POST['timeSizeHour'])*60 + ((int)$_POST['timeSizeMin']))*60;
		$unitimes = date("Y-m-d H:i:s",$unitime);
		$queryStr = $queryStr."'".$unitimes."', ";
	}
	else
	{
		$err='time';
		include 'errdo.php';
		exit;
	}
	
	$infos = testCorrect($_POST['infos']); 
	$queryStr = $queryStr."'".$infos."', ";
	
	$cont = testCorrect($_POST['cont']); 
	$queryStr = $queryStr."'".$cont."'";
	
	$queryStr=$queryStr.");";
	
	/*echo $queryStr;
	exit;*/
	$res = $db->query($queryStr);
	if (!$res != false)
		$db->query("UPDATE `statistics` SET `valuei` = `valuei` + 1 WHERE `id` = 2;");
	$startTab = "#buytime";
}


if (isset($_POST['type']))      //not ajax
{
	/*предложение своего времени*/
	
	$queryStr = "INSERT INTO `salles`(`login`,`time`, `timeStart`, `timeEnd`, `skill`,`position`,`contacts`) VALUES (";
	$sdate = date("Y-m-d H:i:s");
	$queryStr = $queryStr."'".$login."','".$sdate."', ";
	
	if ((testCorrectDate($_POST['timeStartD'])) &&
		(testCorrectTime($_POST['timeStartT'])))
		$queryStr = $queryStr."'".$_POST['timeStartD']." ".$_POST['timeStartT']."', ";
	else
	{
		$err='time';
		include 'errdo.php';
		exit;
	}
	
	if ((testCorrectInt($_POST['timeSizeHour'])) &&
		(testCorrectInt($_POST['timeSizeMin'])))
	{
		$unitimes = strtotime($_POST['timeStartD']." ".$_POST['timeStartT']);
		$unitime = strtotime($unitimes);
		$unitime = $unitimes+(((int)$_POST['timeSizeHour'])*60 + (int)$_POST['timeSizeMin'])*60;
		$unitimes = date("Y-m-d H:i:s",$unitime);
		$queryStr = $queryStr."'".$unitimes."', ";
	}
	else
	{
		$err='time';
		include 'errdo.php';
		exit;
	}
	
	
	$type = testCorrect($_POST['type']);
	$queryStr = $queryStr."'".$type."', ";
	
	$place = testCorrect($_POST['place']);
	$queryStr = $queryStr."'".$place."', ";
	
	$cont = testCorrect($_POST['cont']);
	$queryStr = $queryStr."'".$cont."'";
	
	$queryStr=$queryStr.");";
	
	$res = $db->query($queryStr);
	if ($res!=false)
		$db->query("UPDATE `statistics` SET `valuei` = `valuei` + 1 WHERE `id` = 3;");
	$startTab = "#soldtime";
}

if (isset($_POST['message']))		//not ajax
{
	$queryStr = "INSERT INTO `admmess`(`user`,`time`,`textmess`,`readed`) VALUES (";
	$sdate = date("Y-m-d H:i:s");
	$queryStr = $queryStr."'".$login."','".$sdate."', ";
	
	$textmess = testCorrect($_POST['textmess']);
	$queryStr = $queryStr."'".$textmess."', ";
	$queryStr = $queryStr."'false');";
	$db->query($queryStr);
	$startTab = "#admmess";
	exit;
}

if ($query=='cancelOrder')		//ajax
{	/*удалить запись order-заказ времени*/
	$id = intval($id);
	$resl = $db->query("SELECT login FROM `orders` WHERE id=".$id.";");
	if ($resl!= false)
	{
		$line = $resl->fetch_array();
		if ($login==$line['login'])
			$db->query("DELETE FROM `orders` WHERE id=".$id.";");
	}
	exit;
}


if ($query=='deleteSeller')		//ajax
{
	$id = intval($id);
	$resl = $db->query("SELECT login FROM `salles` WHERE id=".$id.";");
	if ($resl != false)
	{
		$line = $resl->fetch_array();
		if ($login==$line['login'])
			$db->query("DELETE FROM `salles` WHERE id=".$id.";");
	}
	exit;
}



if ($query=='setPass')		//ajax
{
	$rpass = $newpass;
	for ($i=0; $i<strlen($rpass); $i++)
	{
		if (testchar2($rpass[$i])==false)
		{
			echo 'error';
			exit;
		}
	}
	
	$passhash = SHA1($rpass);
	$db->query("UPDATE `users` SET pass='".$passhash."' WHERE login='".$login."';");
	exit;
}

}


function testCorrectInt($intg)
{
	$intt = (int)$intg;
	if ($intt>=0)
			return true;
	return false;
}

function testCorrectDate($date)
{
//"Y-m-d H:i"
	if (isset($date)) return false;
	if (preg_match('(\b([0-9]{4})\-([0-9]{1,2})\-([0-9]{1,2})\b)', $date)) 
		return true;
	else 
		return false;
}

function testCorrectTime($time)
{
	if (preg_match("(\b([0-9]{1,2}):([0-9]{2})\b)", $time)) 
		return true;
	else 
		return false;
}


function testCorrect($stri)
{
	$strv = mysql_escape_sym(htmlspecialchars($stri));
	return $strv;
}



?>