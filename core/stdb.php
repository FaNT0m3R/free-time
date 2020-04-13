<?php //инициализация DB тут

$login_db = "freetime";
$passwd_db = "***************";
$name_db = "freetime";

$db = mysqli_connect("localhost",$login_db,$passwd_db,$name_db);
if (!$db) {
	echo $strerrdb;
	exit();
}
?>