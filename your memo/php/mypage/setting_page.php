<?php
session_start();
require_once("./../config/db.php");
$dbh=db_connect();
?>


<html>

<head>
	<title>Setting page</title>
	<meta charset="utf-8">
	<h1>Setting</h1>
</head>

<body>
	<div>
		<p>AccountID</p>
		<p>利用開始日</p>
	</div>

	<!-- <form　action=""　method="post">
		<a href="unsubscribe_check.php"><button>退会する</button></a>
	</form> -->

</body>
</html>
