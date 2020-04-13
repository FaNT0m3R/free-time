<?php
if ($secur != true)
	exit;

if ($user != 2)
	exit;

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<title> Free Times </title>
<link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="content"  style="width:900px;">
<div class="header"> Free times </div>
<div class="leftp">
	<div class="btn_lmenu" onclick="clickMenu('#pg_admin_home')" > 	 <?=$login;?>	</div>
	<div class="btn_lmenu" onclick="clickMenu('#pg_orders');" > 	 Заказы		</div>
	<div class="btn_lmenu" onclick="clickMenu('#pg_sellers');" > 	 Продавцы	</div>
	<div class="btn_lmenu" onclick="clickMenu('#pg_messages')" > 	 Сообщения	</div>
	<div class="btn_lmenu" onclick="clickMenu('#pg_users')" > 	 Пользователи	</div>
	<div class="btn_lmenu" onclick="logout()" >				   Выйти </div>
	<img src="imgs/clock.png" width="100%">
	
</div>
<div class="rightp" style="width:650px;">
	<div id="pg_admin_home" style="display: block;"> 
		<table>
		<?php 
		$statTable = $db->query("SELECT name,valuei FROM `statistics` ORDER BY `id`;");
		if ($statTable === false)
			$linestat["valuei"] = "";
		else
			$linestat = $statTable->fetch_array();
		?>
		<tr> <td width=400>Всего пользователей: </td><td>
		<?php
		$usrTable = $db->query("SELECT id FROM `users` WHERE bactiv=1;");
		echo $usrTable->num_rows;
		?></td></tr>
		<tr> <td width=400>Заказов свободного времени: </td><td>
		<?php
		//$line = $statTable->fetch_array();
		$usrTable = $db->query("SELECT id FROM `orders` WHERE state='0';");
		echo $usrTable->num_rows;
		
		?></td></tr>
		<tr> <td width=400>Предложений времени: </td><td><?php
		//$line = $statTable->fetch_array();
		$usrTable = $db->query("SELECT id FROM `salles`;");
		echo $usrTable->num_rows;
		
		?></td></tr>
		<tr> <td width=400>Входов на сайт: </td><td><?php
		//$line = $statTable->fetch_array();
		echo $linestat['valuei'];
		?></td></tr>
		</table>
		<br><br>
		<div class="smallzone">
		Со временем в базе данных накапливается
		много устаревшей информации. Примерно раз
		в месяц её стоит удалять.<br>
		<button onclick="clearDB()"> Очистить </button>
		</div>
		<div class="smallzone">
		На случай, если вы хотите сменить свой пароль.<br>
		<input id="newpass" placeholder="новый пароль">
		<button onclick="changePass()"> Сменить пароль </button>
		</div>
		
	</div>
	
	
<?php/*** ЗАКАЗЫ ***************************************************************************************************/?>		
	<div id="pg_orders" style="display: none;">
		<div class="subheader">Заказы</div>
		<div id="orders_all">
		<?php
		$ords = $db->query('SELECT id,time,timeStart, timeEnd, texts, contacts, comments,state FROM `order` WHERE "state"=0 ORDER BY time;');
		if ($ords!=false)
		{
		for ($i=0; $i<$ords->num_rows; $i++){
			$line = $ords->fetch_array();
			
			$minut = (int)((strtotime($line['timeEnd']) - strtotime($line['timeStart'])) /60);
			$hour = (int)($minut/60);
			$minut = $minut - $hour*60;
			
			echo '<div class="item" id="'.$line['id'].'">';
			echo '<span class="messtime">'.$line['time'].'</span><br>';
			echo '<span class="messfreetime">',$line['timeStart'].' - ',$hour,' ',getHourStr($hour),' ';
			if ($minut!=0) 
				echo $minut,' ',getMinutStr($minut);
			echo '</span>'.'<br>';
			echo '<b>Данные: </b>'.$line['texts'].'<br>'.'<b>Контакты: </b>'.$line['contacts'].'<br>';
			echo '<div class="button2" onclick="cancelOrder(\''.$line["id"].'\')">отказать</div>';
			if ($line['state']!=0)
				echo '<div class="button2" onclick="selectSelles(\''.$line['id'].'\')">Изменить исполнителей</div>';
			else
				echo '<div class="button2" onclick="selectSelles(\''.$line['id'].'\')">Выбрать исполнителей</div>';
			echo '</div>';
			
		}
		}
		?>
		</div>
	</div>
	
	
<?php/*** ПРОДАВЦЫ ***************************************************************************************************/?>	
	<div id="pg_sellers" style="display: none;">
		<div class="subheader">Продавцы</div>
		<div id="orders_all">
		<?php
		$ords = $db->query('SELECT id,login,time,timeStart, timeEnd, skill, position, contacts, comments,state FROM `salles` ORDER BY timeStart;');
		if ($ords!=false)
		{
		for ($i=0; $i<$ords->num_rows; $i++){
			$line = $ords->fetch_array();
			
			$minut = (int)((strtotime($line['timeEnd']) - strtotime($line['timeStart'])) /60);
			$hour = (int)($minut/60);
			$minut = $minut - $hour*60;
			
			echo '<div class="item">';
			echo '<span class="messtime">'.$line['time'].'</span><br>';
			echo '<span class="messfreetime">',$line['timeStart'].' - ',$hour,' ',getHourStr($hour),' ';
			if ($minut!=0) 
				echo $minut,' ',getMinutStr($minut);
			echo '</span>';
			if ($line['state']==0)
				echo '<b> свободен </b>';
			else if ($line['state']==1)
				echo '<b> частично занят </b>';
			else if ($line['state']==2)
				echo '<b> занят </b>';
			echo '<br><b>Данные: </b>'.$line['skill'].'<br>'.'<b>Место расположения: </b>'.$line['position'].'<br>'.'<b>Контакты: </b>'.$line['contacts'],'<br>';
			if ($line['state']!=0)
				echo '<a href="core/view.php?id='.$line['id'].'">Его задачи</a><br>';
			echo '<div class="button2" onclick="deleteSeller(\''.$line['id'].'\')">Удалить</div>';
			echo '</div>';
		}
		}
		?>
		</div>
	</div>
	
	<div id="pg_messages" style="display: none;">
	<div class="subheader">Сообщения </div>
	
	<?php
/*** СООБЩЕНИЯ ***************************************************************************************************/	
	$mess = $db->query('SELECT  id, user, textmess, time, readed FROM `admmess`');
	if ($mess!=false)
	{
	for ($i=0; $i<$mess->num_rows; $i++){
		$line = $mess->fetch_array();
		if ($line[4]==1)
			echo '<div id="'.$line['id'].'" style="padding:5px;">';
		else
			echo '<div style="background-color=#DDD;" id="'.$line['id'].'">';
			
		echo '<div style="float:left; clear:left; padding:5px; width:100px;">'.$line['time'].' '.$line['user'].'</div>';
		
		echo '<div style="float:left; padding:5px; width:400px;">'.$line['textmess'].'<br>';
		echo '<span class="button2" onclick="removeMess('.$line["id"].')">удалить</span></div>';
		echo '</div>';
	}
	}
	$mess = $db->query('UPDATE `admmess` SET readed = true;');
	?>
	</div>
	
<?php/*** ПОЛЬЗОВАТЕЛИ ***************************************************************************************************/?>	
	<div id="pg_users" style="display: none;">
	<div class="subheader">Пользователи </div>
	
	<table style="padding:5px;">
	<col valign="top">
	<col valign="top">
	<col valign="top">
	<tr><td><b>Логин</b></td><td><b>e-mail</b></td><td><b>Дата регистрации</b></td><td><b>Последний вход</b></td></tr>
	
	<?php
	$mess = $db->query('SELECT  id,login, email, badmin, bactiv, dataregistr, datalast FROM `users`');
	if ($mess!=false)
	{
	
	for ($i=0; $i<$mess->num_rows; $i++){
		$line = $mess->fetch_array();
		echo '<tr id="usr'.$line['id'].'">';
		echo '<td style="padding:5px;"><b>'.$line['login'].'</b><br>';
		
		echo '<button onclick="setPass('.$line['id'].')"> Установить</button><input id="np'.$line['id'].'" placeholder="новый пароль">';
		echo '<button onclick="delUser('.$line['id'].')"> Удалить пользователя</button><br>';
		
		echo '</td><td>'.$line['email'].'</td><td>'.$line['dataregistr'].' </td><td>'.$line['datalast'].'</td>';
		echo '</tr>';
	}
	echo '</table>';
	}
	?>
	</div>
	
	
</div>
</div>
<script language="javascript"> 

var pgOldValue = "#pg_admin_home";


function clickMenu(idEl)
{
	if (pgOldValue != idEl)
	{
		$(pgOldValue).hide();
		$(idEl).show();
		pgOldValue = idEl;
	}
}

function logout()
{
	document.cookie = "var1=0;max-age=0;";
	document.cookie = "var2=0;max-age=0;";
	document.cookie = "PHPSESSID=0;max-age=0;";
	document.location = "index.php";
}


function cancelOrder(id) {
	$("#"+id)[0].style.color="#CCC";

	var req = getXmlHttp(); 
	req.open('GET', 'index.php?query=cancelOrder&id='+id, true); 
	req.onreadystatechange = function() { 
		if (req.readyState == 4) {
			if(req.status == 200) {
				$("#"+id)[0].remove();
			}
			else
			{
				$("#"+id)[0].style.color="#000";
			}
        }
    }
    req.send(null);
}

function selectSelles(id)
{
	//если нажали на выбор продавцов
	//то перейти на страницу, которая выведет всех, кто входит по времени.
	location = 'core/sels.php?idorder='+id;
	
}

function removeMess(id) {
	$("#"+id)[0].style.color="#CCC";

	var req = getXmlHttp(); 
	req.open('GET', 'index.php?query=removeMess&id='+id, true); 
	req.onreadystatechange = function() { 
		if (req.readyState == 4) {
			if(req.status == 200) {
				$("#"+id)[0].remove();
			}
			else// тут можно добавить else с обработкой ошибок запроса
			{
				$("#"+id)[0].style.color="#000";
			}
        }
    }
    req.send(null);
}


function clearDB()
{
	var req = getXmlHttp(); 
	req.open('POST', 'index.php?query=clearDB', true); 
	req.onreadystatechange = function() { 
		if (req.readyState == 4) {
			if(req.status == 200)
	 			location = 'index.php';
				//alert(req.responseText);
			else
				alert('Произошла ошибка');
        }
    }
    req.send(null);

}



function changePass()
{
	var newpass = $("#newpass")[0].value;

	var req = getXmlHttp(); 
	req.open('POST', 'index.php?query=setPass&newpass='+newpass, true); 
	req.onreadystatechange = function() { 
		if (req.readyState == 4) {
			if(req.status == 200) {
				if (req.responseText!="error")
					alert("Пароль измененён");
	 			else
					alert("Произошла ошибка. Пароль не измененён.");
			}
			else
			{
				alert("Произошла ошибка. Пароль не измененён.");
			}
        }
    }
    req.send(null);

}


function setPass(id)
{
	var newpass = $("#np"+id)[0].value;

	var req = getXmlHttp(); 
	req.open('POST', 'index.php?query=setUserPass&id='+id+'&newpass='+newpass, true); 
	req.onreadystatechange = function() { 
		if (req.readyState == 4) {
			if(req.status == 200) {
				if (req.responseText!="error")
					alert("Пароль измененён");
				else
					alert("Произошла ошибка. Пароль не измененён.");
			}
			else
			{
				alert("Произошла ошибка. Пароль не измененён.");
			}
        }
    }
    req.send(null);

}


function delUser(id){
	$("#usr"+id)[0].style.color="#CCC";

	var req = getXmlHttp(); 
	req.open('GET', 'index.php?query=delUser&id='+id, true); 
	req.onreadystatechange = function() { 
		if (req.readyState == 4) {
			if(req.status == 200) {
				$("#usr"+id)[0].remove();
			}
			else// тут можно добавить else с обработкой ошибок запроса
			{
				$("#usr"+id)[0].style.color="#000";
			}
        }
    }
    req.send(null);
}





function getXmlHttp(){
  var xmlhttp;
  try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}
</script>
<script language="javascript" src="js/jquery-3.0.0.js"></script>



</body>
</html>
<?php
exit;


function clearSess()
{


}
?>
