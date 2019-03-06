<?php
session_start();

header("Content-type: text/html; charset=utf-8");

//クロスサイトリクエストフォージェリ（CSRF）対策
$_SESSION['token'] = bin2hex(str_shuffle(32));
$token = $_SESSION['token'];

/*
base64_encode(): MIME base64 方式でデータをエンコードする
str_shuffle():文字列をシャッフルします。 考えられるすべての順列のうちのひとつを作成します。（脆弱性高い）
→opensslを有効にできず推奨されるopenssl_random_pseudo_bytesが使えなかった。
*/

//クリックジャッキング対策
header('X-FRAME-OPTIONS: SAMEORIGIN');


?>

<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="./../../css/register.css">
  <title>メール登録画面</title>
  <meta charset="utf-8">
</head>

<body>
  <header>
    <div class="container">
      <a href="./../../index.php"><img src="./../../img/logo_01.png" width="260"></a>
    </div>
  </header>

  <main>
  <h1>メール登録</h1>

  <form action="./registration_mail_check.php" method="post">

    <p>メールアドレス：<input type="text" name="mail" size="50" required></p>

    <input type="hidden" name="token" value="<?=$token?>">
    <input type="submit" value="登録する">

  </form>
</main>

<footer>
  <small>(c)2019 Katsumi Nagaike ALL RIGHTS RESERVED.</small>
</footer>

</body>
</html>
