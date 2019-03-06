<?php
session_start();
session_destroy();
$_SESSION = array();
?>

<!DOCTYPE html>
<html>

<head>
  <title>logout.php</title>
  <link rel="stylesheet" type="text/css" href="mypage.css">
  <meta charset="utf-8">
</head>

<body>

  <header><div class="container"><a href="./../../index.php"><img src="./../../img/logo_01.png" width="260"></a></div></header>

  <main class="container">

    <div class="center"><br><br><br>
      ログアウトしました。
      <br>
      ご利用ありがとうございました。
      <br><br><br>
      <hr>
      <br>
      <p><a href="./../../index.php">ログイン画面へ戻る</a></p>
    </div>

  </main>

  <footer><small>(c)2019 Katsumi Nagaike ALL RIGHTS RESERVED.</small></footer>

</body>
</html>
