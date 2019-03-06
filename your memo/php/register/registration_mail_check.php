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
}else{
	//POSTされたデータを変数に入れる
	$mail = isset($_POST['mail']) ? $_POST['mail'] : NULL;

	//メール入力判定
	if ($mail == ''){
		$errors['mail'] = "メールが入力されていません。";
	}else{
		if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)){
			$errors['mail_check'] = "メールアドレスの形式が正しくありません。";
		}
	}

	/*
	ここで本登録用のmemberテーブルにすでに登録されているmailかどうかをチェックする。
	$errors['member_check'] = "このメールアドレスはすでに利用されております。";
	*/
	$sql = 'SELECT * FROM member ORDER BY id DESC';
	$stmt = $dbh->query($sql);
	foreach ($stmt as $row) {
		if($row['mail'] == $mail){
			$errors['member_check'] = "このメールアドレスはすでに利用されております。";
			$_SESSION = array();
			if (isset($_COOKIE["PHPSESSID"])) {
				setcookie("PHPSESSID", '', time() - 1800, '/');
			}
			session_destroy();

		}
	}

}


if (count($errors) === 0){

	$urltoken = hash('sha256',uniqid(rand(),1));
	$url = "http://tt-844.99sv-coco.com/mission_6/php/register/registration_form.php"."?urltoken=".$urltoken;

	//ここでデータベースに登録する
	try{
		//例外処理を投げる（スロー）ようにする
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		$statement = $dbh->prepare("INSERT INTO pre_member (urltoken,mail,date) VALUES (:urltoken,:mail,now() )");

		//プレースホルダへ実際の値を設定する
		$statement->bindValue(':urltoken', $urltoken, PDO::PARAM_STR);
		$statement->bindValue(':mail', $mail, PDO::PARAM_STR);
		$statement->execute();

		//データベース接続切断
		$dbh = null;

	}catch (PDOException $e){
		print('Error:'.$e->getMessage());
		die();
	}

	//メールの宛先
	$mailTo = $mail;

	//Return-Pathに指定するメールアドレス
	$returnMail = 'yourmemo@sample.com';//

	$name = "yourmemo";
	$mail = 'yourmemo@sample.com';
	$subject = "【yourmemo】会員登録用URLのお知らせ";

	$body = <<< EOM
	ユーザー登録確認

	以下のURLからご登録ください。

	{$url}

	※ユーザー登録の有効期間は24時間です。
EOM;

	mb_language('ja');
	mb_internal_encoding('UTF-8');

	//Fromヘッダーを作成
	$header = 'From: ' . mb_encode_mimeheader($name). ' <' . $mail. '>';

	if (mb_send_mail($mailTo, $subject, $body, $header, '-f'. $returnMail)) {

		//セッション変数を全て解除
		$_SESSION = array();

		//クッキーの削除
		if (isset($_COOKIE["PHPSESSID"])) {
			setcookie("PHPSESSID", '', time() - 1800, '/');
		}

		//セッションを破棄する
		session_destroy();

		$message = "メールをお送りしました。24時間以内にメールに記載されたURLからご登録下さい。";

	} else {
		$errors['mail_error'] = "メールの送信に失敗しました。";
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<title>メール確認</title>
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
		<h1>メール確認</h1>

		<?php if (count($errors) === 0): ?>

			<p><?=$message?></p>

			<p>↓このURLが記載されたメールが届きます。</p>
			<a href="<?=$url?>"><?=$url?></a>

		<?php elseif(count($errors) > 0): ?>

			<?php
			foreach($errors as $value){
				echo "<p>".$value."</p>";
			}
			?>
			<!-- <input type="button" value="戻る" onClick="history.back()"> -->
			<input type="button" value="戻る" onClick="location.href='./registration_mail_form.php'">

		<?php endif; ?>
	</main>

	<footer>
		<small>(c)2019 Katsumi Nagaike ALL RIGHTS RESERVED.</small>
	</footer>

</body>
</html>
