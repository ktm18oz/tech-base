<?php
session_start();
require_once("db.php");
$dbh = db_connect();

$sql = 'SELECT * FROM member ORDER BY id DESC';
$stmt = $dbh->query($sql);
$result = $stmt->fetchAll();
if(!empty($result)){
  echo 'contents in table [member] is:<br />';
  echo '<pre>';
  print_r($result);
  echo '<pre>';
  echo '<hr>';
}else{
  echo 'no contents in table [member].<br />';
}

$dbh = null;
session_destroy();
?>
