<?session_start();
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
$id = (int)$_GET['id'];
$tabl = $db->query("SELECT orderid FROM `exchange` WHERE sallesid=".$id.";");


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
<?

for ($i=0; $i<$tabl->num_rows; $i++){
	$sid = $tabl->fetch_array();
	$ordt = $db->query("SELECT time,timeStart, timeEnd, texts,contacts FROM `order` WHERE id='".$sid['orderid']."';");
	if ($ordt!=false){
		for ($j=0; $j<$ordt->num_rows; $j++){
			$line = $ordt->fetch_array();
			$minut = (int)((strtotime($line['timeEnd']) - strtotime($line['timeStart'])) /60);
			$hour = (int)($minut/60);
			$minut = $minut - $hour*60;
			
			echo '<div>';
			echo '<span class="messtime">'.$line['time'].'</span><br>';
			echo '<span class="messfreetime">',$line['timeStart'].' - ',$hour,' ',getHourStr($hour),' ';
			if ($minut!=0) 
				echo $minut,' ',getMinutStr($minut);
			echo '</span>'.'<br>';
			echo $line['texts'].'<br>'.$line['contacts'];
			echo '</div>';
		}
	};
}
?>


</div>
</body>
</html>

<?
$db->close();
function getHourStr($hour)
{
	$one = $hour%10;
	if ($one == 1)
		return "час";
	if (($one >= 2) && ($one <= 4))
		return "часа";
	return "часов";
}

function getMinutStr($minut)
{
	$one = $minut%10;
	if ($one == 1)
		return "минута";
	if (($one >= 2) && ($one <= 4))
		return "минуты";
	return "минут";
}

?>