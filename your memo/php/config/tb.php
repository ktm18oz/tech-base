<?php
session_start();
$_SESSION = array();
require_once("db.php");
$dbh = db_connect();

$pre_member = 'pre_member';
$member = 'member';
?>

<html lang='ja'>
<head>
	<meta charset='utf-8'>
	<title>Table management page</title>
</head>

<body>
	<h4>Table management page</h4>
	<form method='post'><input type='submit' name ='dlt_btn' value='Delete all tables'></form>
	<form method='post'><input type='submit' name ='crt_btn' value='create table'></form>
	<form method='post'><input type='submit' name ='shw_btn' value='show table'></form>
	<form method='post'><input type='submit' name ='return_btn' value='Return'></form>
	<hr>
</body>
</html>

<?php
echo "現在DB上にあるテーブルは<br />";
$sql= 'SHOW TABLES';
$result = $dbh->query($sql);
foreach ($result as $row){
	var_dump($row[0]); echo '<br>';
}
echo "<hr>";
?>

<?php

if(isset($_POST['crt_btn'])){
	require_once("tb_create.php");
}

if(isset($_POST['dlt_btn'])){
	require_once("tb_delete.php");
}

if(isset($_POST['shw_btn'])){
	require_once("tb_show.php");
}

if(isset($_POST['return_btn'])){
	$dbh = null;
	session_destroy();
	header("Location: ./../../index.php");
}
?>
