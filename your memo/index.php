<?php
session_start();
if( isset($_SESSION['user']) != "") {
	// ログイン済みの場合は、マイページへリダイレクト
	header("Location: ./php/mypage/mypage.php");
}
//DB接続
require_once("./php/config/db.php"); $dbh = db_connect();

//エラー文初期化
$errors = array();


//変数
$login_btn      = $_POST['login_btn'];
$login_mail     = $_POST['login_mail'];
$login_password = $_POST['login_password'];
$register_btn   = $_POST['register_btn'];

//関数

function check_mail($login_mail, $login_password) {
	require_once("./php/config/db.php"); $dbh = db_connect();
	$sql  = 'SELECT * FROM member WHERE mail =:mail';
	$stmt = $dbh->prepare($sql);
	$stmt -> bindParam(':mail', $login_mail, PDO::PARAM_STR);
	$stmt -> execute();
	$row_count = $stmt->rowCount();
	if($row_count === 0 ) {
		$er_msg1 = "登録されていないメールアドレスです。";
		echo $er_msg1;
		return $er_msg1;
	} else {
		$results = $stmt->fetchAll();
		foreach ($results as $row) {
			if($login_password != $row['password']) {
				$er_msg2 = "パスワードが違います。";
				echo $er_msg2;
				return $er_msg2;
			} else {
				$_SESSION['id']      = $row['id'];
				$_SESSION['account'] = $row['account'];
				$_SESSION['mail']    = $row['mail'];
				$_SESSION['password']= $row['password'];
			}
		}
	}
}

function error_check($errors) {
	if(count($errors) === 0 ){
		header("Location: ./php/mypage/mypage.php");
		exit();
	}
}
?>

<!DOCTYPE html>
<html lang = "ja">

<head>
	<title>ログインページ</title>
	<meta charset = "utf-8">
	<link rel = "stylesheet" type = "text/css" href = "./css/index.css">
</head>

<body>

	<header><div class="container"><img src="./img/logo_01.png" width="260"></div></header>
	<main class="container">
		<h2>ようこそ your memo へ</h2>

		<!-- /////////////////////////////////////////////////////////////// -->
		<div class="subcontainer">

			<section id="login_section" width="400" height="300" color="#00bfff"><div class="left">
				<h2>会員の方</h2>

				<form method="post">
					E-mail address<br />
					<input size = "50" type = "mail"  	 name = "login_mail"     placeholder = "○○○@○○○.com" required><br /><br />
					Password<br />
					<input size = "50" type = "password" name = "login_password" placeholder = "○○○○○○○○"    required>

					<div class = "btn_container"><input class = "btn" type = "submit" name = "login_btn" value = "  Login  "><br /><br />
						<?php if( isset( $login_btn ) ) { $errors = check_mail($login_mail, $login_password); error_check($errors); }	?>
					</div></form></div></section>

					<!--  -->

					<section id="login_section" width="400" height="300" color="skyblue"><div class="right">
						<h2>はじめての方</h2>

						<form method="post"><br />
							こちらから会員登録(無料)が必要です。<br /><br /><br /><br />

							<div class="btn_container"><form action="" method="post"><input type="submit" name="register_btn" value=" Register "></form>
								<?= $register = isset( $register_btn ) ? header("Location: ./php/register/registration_mail_form.php") : null ?>
							</div></form></div></section>

						</div>
						<!-- /////////////////////////////////////////////////////////////// -->

						<!-- <a href="./php/config/tb.php"><button>See table conttents</button></a> -->
					</main>
					<footer><small>(c)2019 Katsumi Nagaike ALL RIGHTS RESERVED.</small></footer>

				</body>
				</html>
