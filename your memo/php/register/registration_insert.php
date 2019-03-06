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

//エラーメッセージの初期化
$errors = array();

if(empty($_POST)) {
	header("Location: ./registration_mail_form.php");
	exit();
}

$mail = $_SESSION['mail'];
$account = $_SESSION['account'];

//パスワードのハッシュ化
$password_hash = $_SESSION['password'];

//ここでデータベースに登録する
try{
	//例外処理を投げる（スロー）ようにする
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	//トランザクション開始
	$dbh->beginTransaction();

	//memberテーブルに本登録する
	$statement = $dbh->prepare("INSERT INTO member (account,mail,password) VALUES (:account,:mail,:password_hash)");
	//プレースホルダへ実際の値を設定する
	$statement->bindValue(':account', $account, PDO::PARAM_STR);
	$statement->bindValue(':mail', $mail, PDO::PARAM_STR);
	$statement->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
	$statement->execute();

	//pre_memberのflagを1にする
	$statement = $dbh->prepare("UPDATE pre_member SET flag=1 WHERE mail=(:mail)");
	//プレースホルダへ実際の値を設定する
	$statement->bindValue(':mail', $mail, PDO::PARAM_STR);
	$statement->execute();

	// トランザクション完了（コミット）
	$dbh->commit();

	/*
	登録完了のメールを送信
	*/
	if (count($errors) === 0){

		//$url = "http://tt-844.99sv-coco.com/register/registration_form.php"; //top page?

		$mailTo = $mail;
		$returnMail = 'yourmemo@sample.com';
		$name = "yourmemo";
		$mail = 'yourmemo@sample.com';
		$subject = "【yourmemo】会員登録完了のお知らせ";
		$body = "[ ".$account." 様のユーザー登録が完了いたしました]<br><br>この度はyourmemoにご登録いただき誠にありがとうございました。";

		mb_language('ja');
		mb_internal_encoding('UTF-8');
		$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';
		if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {
			$_SESSION = array();
			if (isset($_COOKIE["PHPSESSID"])) {
				setcookie("PHPSESSID", '', time() - 1800, '/');
			}
			session_destroy();
			$dbh = null;

			$message = "この度はyourmemoにご登録いただき誠にありがとうございました。<br>会員登録完了メールをお送りしましたのでご確認くださいませ。";

		} else {
			$errors['mail_error'] = "メールの送信に失敗しました。";
		}
	}


}catch (PDOException $e){
	//トランザクション取り消し（ロールバック）
	$dbh->rollBack();
	$errors['error'] = "もう一度やりなおして下さい。";
	print('Error:'.$e->getMessage());
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>会員登録完了</title>
	<link rel="stylesheet" type="text/css" href="./../../css/register.css">
	<meta charset="utf-8">
</head>
<body>

	<header>
		<div class="container">
			<a href="./../../index.php"><img src="./../../img/logo_01.png" width="260"></a>
		</div>
	</header>

	<main>

		<?php if (count($errors) === 0): ?>
			<h1>会員登録完了</h1>

			<p><?=$message?></p>
			<p><a href="./../../index.php">ログイン画面へ戻る</a></p>

		<?php elseif(count($errors) > 0): ?>

			<?php
			foreach($errors as $value){
				echo "<p>".$value."</p>";
			}
			?>

		<?php endif; ?>

	</main>

	<footer>
		<small>(c)2019 Katsumi Nagaike ALL RIGHTS RESERVED.</small>
	</footer>


</body>
</html>
