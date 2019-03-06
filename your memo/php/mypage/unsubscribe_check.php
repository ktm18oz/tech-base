<?php
	session_start();

	require_once("./../config/db.php");
	$dbh = db_connect();


?>


<html>
	<head>
	  <title>unsubscribe</title>
	  <meta charset="utf-8">
	  <h1>退会確認画面</h1>
	</head>

	<body>

	  <div>
	    <p>Account ID：<?= $_SESSION['acount']?></p>
			<p>利用開始日　：</p>

	  </div>
<div>
	退会すると今までの記録が全て削除されます。<br>
	一度退会すると現在ご使用になられているメールアドレスでの登録はできなくなります。<br>

	<br><br>
	<hr>



</div>
	<form action="" method="post">
	 <a href="unsubscribe.php"><input type="submit" name="unsubscribe" value="了解して退会する"></a>
	</form>

	</body>
	</html>

<?php
	if(isset($_GET['delete'])) { // 会員退会ボタンが押下されたときに実行
		$query = "DELETE FROM users WHERE user_id=".$_SESSION['user']."";// ユーザーIDをキーにDBからユーザ情報を削除
		$result = $pdo->query($query);

		if (!$result) {
		  print('クエリーが失敗しました。' . $pdo->error);
		  $pdo->close();
		  exit();
		}
	}

	if(isset($_GET['delete'])) { // URLクエリパラメータがdeleteだった場合、
	  session_destroy();
	  unset($_SESSION['user']);
	  header("Location: index.php?delete");
	} elseif(isset($_GET['logout'])) { // URLクエリパラメータがlogoutだった場合、
	  session_destroy();
	  unset($_SESSION['user']);
	  header("Location: index.php");
	} else {
	  header("Location: index.php");
	}
?>
