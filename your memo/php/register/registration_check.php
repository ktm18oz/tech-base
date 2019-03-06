<!DOCTYPE html>
<html>
<head>
	<title>会員登録確認</title>
	<link rel="stylesheet" type="text/css" href="./../../css/register.css">
	<meta charset="utf-8">
</head>
<body>

<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策のトークン判定
if ($_POST['token'] != $_SESSION['token']){
	echo "不正アクセスの可能性あり";
	exit();
}

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');

//データベース接続
require_once("./../config/db.php");
$dbh = db_connect();

//前後にある半角全角スペースを削除する関数
function spaceTrim ($str) {
	// 行頭
	$str = preg_replace('/^[ 　]+/u', '', $str);
	// 末尾
	$str = preg_replace('/[ 　]+$/u', '', $str);
	return $str;
}

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: ./registration_mail_form.php");
	exit();
}else{
	//POSTされたデータを各変数に入れる
	$account = isset($_POST['account']) ? $_POST['account'] : NULL;
	$password = isset($_POST['password']) ? $_POST['password'] : NULL;
	$password2 = isset($_POST['password2']) ? $_POST['password2'] : NULL;

	//前後にある半角全角スペースを削除
	$account = spaceTrim($account);
	$_SESSION['account'] = $account;
	$password = spaceTrim($password);
	$password2 = spaceTrim($password2);

	//アカウント入力判定
	$sql = 'SELECT * FROM member ORDER BY id DESC';
	$stmt = $dbh->query($sql);
	foreach ($stmt as $row) {
		if($account == $row['account']){
			$errors['member_check'] = "このアカウント名はすでに利用されております。";
			$_SESSION = array();
			session_destroy();
		}
	}

	if ($account == ''){
		$errors['account'] = "アカウントが入力されていません。";
		$_SESSION = array();
		session_destroy();
	}elseif(mb_strlen($account)>24){
		$errors['account_length'] = "アカウントは12文字以内で入力して下さい。";
		$_SESSION = array();
		session_destroy();
	}

	//パスワード入力判定
	if ($password == ''){
		$errors['password'] = "パスワードが入力されていません。";
	}elseif(!preg_match('/^[0-9a-zA-Z]{5,30}$/', $_POST["password"])){
		$errors['password_length'] = "パスワードは半角英数字の5文字以上30文字以下で入力して下さい。";
	}elseif($password !== $password2){
		$errors['password2_wrong'] = "確認用パスワードが違います。";
	}else{
		$password_hide = str_repeat('*', strlen($password));
	}

}

//エラーが無ければセッションに登録
if(count($errors) === 0){
	//$_SESSION['account'] = $account;
	$_SESSION['password'] = $password;
}

?>

	<header>
		<div class="container">
			<a href="./../../index.php"><img src="./../../img/logo_01.png" width="260"></a>
		</div>
	</header>

	<main>

		<h1>会員登録確認</h1>

		<?php if (count($errors) === 0): ?>

			<div class="table">
				<form action="./registration_insert.php" method="post">
					<table border="1" width="800" cellspacing="0" cellpadding="5" bordercolor="#333333" margin="auto">

						<tr><td class="tb_left">　メールアドレス：</td>
							<td><?=htmlspecialchars($_SESSION['mail'], ENT_QUOTES)?></td>
						</tr>
						<tr><td class="tb_left">　アカウント名(半角英数12文字以内)：</td>
							<td><?=htmlspecialchars($account, ENT_QUOTES)?></td>
						</tr>
						<tr><td class="tb_left">　パスワード(半角英数字5~30文字以内)：</td>
							<td><?=$password_hide?></td>
						</tr>
					</table>
					<br /><br />

						<input type="button" value="戻る" onClick="history.back()">
						 　
						<input type="hidden" name="token" value="<?=$_POST['token']?>">
						<input type="submit" value="登録する">

					</form>

				<?php elseif(count($errors) > 0): ?>

					<?php
					foreach($errors as $value){
						echo "<p>".$value."</p>";
					}
					?>

					<!-- <input type="button" value="戻る" onClick="history.back()"> -->
					<input type="button" value="戻る" onClick="location.href='./registration_form.php'">

				<?php endif; ?>
				<?php $dbh = null; ?>
			</main>

			<footer>
				<small>(c)2019 Katsumi Nagaike ALL RIGHTS RESERVED.</small>
			</footer>


		</body>
		</html>
