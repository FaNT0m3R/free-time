<?//инициализация DB тут
$name_db = "user2031337_freetime";
$login_db = "freetimer";
$passwd_db = "rPtTasdh355";

$db = mysqli_connect("localhost",$login_db,$passwd_db,$name_db);
if ($db->connect_errno) {
	echo $strerrdb;
	exit();
} 	
?>