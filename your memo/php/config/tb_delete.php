<?php
session_start();
require_once("db.php");
$dbh = db_connect();

$sql = 'DROP TABLE IF EXISTS member';
$stmt= $dbh->query($sql);
$stmt->execute();

$sql = 'DROP TABLE IF EXISTS pre_member';
$stmt= $dbh->query($sql);
$stmt->execute();

$sql = 'DROP TABLE IF EXISTS ___memolist';
$stmt= $dbh->query($sql);
$stmt->execute();

$dbh = null;
session_destroy();
?>
