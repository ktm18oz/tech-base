<?php
session_start();
require_once("db.php");
$dbh=db_connect();

$_SESSION['my_id']      = $_SESSION['id'];
$_SESSION['my_account'] = $_SESSION['account'];
$_SESSION['my_mail']    = $_SESSION['mail'];
$_SESSION['my_pass']    = $_SESSION['password'];

try {
		$memo_tb = $_SESSION['my_id']."_".$_SESSION['my_account']."_memolist";
		$sql = 'CREATE TABLE IF NOT EXISTS '.$memo_tb
		.' ('
		.'id 					INT 				NOT NULL AUTO_INCREMENT PRIMARY KEY, '
		.'memo 				VARCHAR(50) NOT NULL,'
		.'category 		VARCHAR(10) NOT NULL,'
		.'due_date 		DATE		 		NOT NULL,'
		.'create_date DATE 				NOT NULL'
		.')';
		$stmt = $dbh->query($sql);
} catch(PDOException $e) {
	echo $e->getMessage();
	die();
}


$dbh = null;

?>
