<?php
session_start();
require_once("db.php");
$dbh = db_connect();


$sql = 'CREATE TABLE IF NOT EXISTS pre_member'
.'('
.'id 				INT 					NOT NULL AUTO_INCREMENT PRIMARY KEY,'
.'urltoken	VARCHAR(128)	NOT NULL,'
.'mail 			VARCHAR(50) 	NOT NULL,'
.'date 			DATETIME 			NOT NULL,'
.'flag 			TINYINT(1) 		NOT NULL DEFAULT 0'
.')'
.'ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;';
$stmt = $dbh->query($sql);

$sql = 'CREATE TABLE IF NOT EXISTS member'
.'('
.'id 				INT 					NOT NULL AUTO_INCREMENT PRIMARY KEY,'
.'account 	VARCHAR(50) 	NOT NULL,'
.'mail 			VARCHAR(50) 	NOT NULL,'
.'password 	VARCHAR(128) 	NOT NULL,'
.'flag 			TINYINT(1) 		NOT NULL DEFAULT 1'
.')'
.'ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;';
$stmt = $dbh->query($sql);

$dbh = null;
session_destroy();
?>
