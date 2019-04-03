<?php
//(3-1)MySQLに接続する
$dsn = 'mysql:dbname='db_name'; host=localhost; charset = utf8';
$user = 'user_name';
$password = 'password';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
?>

<html lang='ja'>
<head>
  <meta charset='utf-8'>
  <title>mission_4</title>
</head>
<body>
  <h1>簡易掲示板</h1>
  <h5>Katsumi N.<h5>
  <!-- 投稿フォーム -->
  <h5><form method='post'>
    <!-- 名前 -->
    <input type='text' name='name' placeholder='Name'
    value='<?php
    if(isset($_POST['edit_btn'])){
      if(!empty($_POST['edit_num']) && !empty($_POST['edit_pwd'])){
        $id = $_POST['edit_num'];
        $edit_pwd = $_POST['edit_pwd'];
        $sql = 'SELECT * FROM tbPost ORDER BY cnt ASC';
        $stmt = $pdo->query($sql);
        foreach ($stmt as $row) {
          //pwd正誤判断
          if($row[0]==$id){
            if($row[4] == $edit_pwd){
              $sql = 'SELECT * FROM tbPost WHERE cnt=:id';
              $stmt = $pdo->prepare($sql);
              echo $row[1];
            }
          }
        }
      }
    }
    // else{echo 'alex';}
    ?>' required><br />

    <!-- コメント -->
    <input type='text' name='comment' placeholder='Comment'
    value='<?php
    if(isset($_POST['edit_btn'])){
      if(!empty($_POST['edit_num']) && !empty($_POST['edit_pwd'])){
        $id = $_POST['edit_num'];
        $edit_pwd = $_POST['edit_pwd'];
        $sql = 'SELECT * FROM tbPost ORDER BY cnt ASC';
        $stmt = $pdo->query($sql);
        foreach ($stmt as $row) {
          //pwd正誤判断
          if($row[0]==$id){
            if($row[4] == $edit_pwd){
              $sql = 'SELECT * FROM tbPost WHERE cnt=:id';
              $stmt = $pdo->prepare($sql);
              echo $row[2];
            }
          }
        }
      }
    }
    // else{echo 'new comment';}
    ?>' required>

    <!-- hidden -->
    <!-- <input type='hidden' name='holder' 確認のためCO -->
    <input type='hidden' name='holder'
    value='<?php
    if(isset($_POST['edit_btn'])){
      if(!empty($_POST['edit_num']) && !empty($_POST['edit_pwd'])){
        $id = $_POST['edit_num'];
        $edit_pwd = $_POST['edit_pwd'];
        $sql = 'SELECT * FROM tbPost ORDER BY cnt ASC';
        $stmt = $pdo->query($sql);
        foreach ($stmt as $row) {
          //pwd正誤判断
          if($row[0]==$id){
            if($row[4] == $edit_pwd){
              $sql = 'SELECT * FROM tbPost WHERE cnt=:id';
              $stmt = $pdo->prepare($sql);
              echo $row[0];
            }
          }
        }
      }
    }
    ?>'><br />

    <!-- パスワード-->
    <input type='text' name='pwd' placeholder='set your password' required>
    <input type='submit' name ='sbmt_btn' value='Submit'>
  </form></h5>

  <!-- 編集フォーム -->
  <h5><form method='post' >
    <input type='number' name='edit_num' placeholder='edit number' required><br />
    <input type='text' name='edit_pwd' placeholder='password required' required>
    <input type='submit' name ='edit_btn' value='Edit'>
  </form></h5>

  <!-- 削除フォーム -->
  <h5><form method='post'>
    <input type='number' name='dlt_num' placeholder='delete number' required><br />
    <input type='text' name='dlt_pwd' placeholder='password required' required>
    <input type='submit' name ='dlt_btn' value='Delete'>
  </form></h5>

</body>
</html>

<?php
//(3-2)投稿のデータを格納するテーブルを作る
$sql = 'CREATE TABLE IF NOT EXISTS tbPost'
.'('
.'cnt INT NOT NULL AUTO_INCREMENT primary key,'
.'name varchar(32),'
.'comment TEXT,'
.'date TEXT,'
.'pwd TEXT'
.');';
$stmt = $pdo->query($sql);

//(3-3) テーブル一覧を表示
// $sql_show = 'SHOW TABLES';
// $result = $pdo->query($sql_show);//object(PDOStatement)+array type
// foreach ($result as $row){
//   var_dump($row[0]); echo '<br>';
// }

//(3-4) 意図した内容のテーブルが作成されているか確認する
// $sql ='SHOW CREATE TABLE tbPost';
// $result = $pdo -> query($sql);
// foreach ($result as $row){
//   for($i=0;$i<count($row);$i++){
//     echo $row[$i];
//   }
// }
// echo '<hr>';

//(3-5)tbPostにデータを挿入する:投稿機能＋編集機能
if(isset($_POST['sbmt_btn'])){
  if(!empty($_POST['comment']) && !empty($_POST['name'])){
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $date = date('Y/n/j G:i:s');
    $pwd = $_POST['pwd'];
    $edit_id = $_POST['holder'];
    $edit_pwd = $_POST['edit_pwd'];

    // 投稿機能
    if(empty($_POST['holder'])){
      $stmt = $pdo -> prepare('INSERT INTO tbPost (cnt,name,comment,date,pwd) VALUES (:cnt,:name,:comment,:date,:pwd)');
      $stmt -> bindParam(':cnt', $cnt, PDO::PARAM_INT);
      $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
      $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
      $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
      $stmt -> bindParam(':pwd', $pwd, PDO::PARAM_STR);
      $stmt -> execute(); //excuteメソッドでクエリの実行

      $sql = 'SELECT * FROM tbPost ORDER BY cnt DESC';
      $stmt = $pdo->query($sql);
      $results = $stmt->fetch();

      echo 'New post has successfully sent as post no.'.$results[0].'<hr>';
    }elseif(!empty($_POST['holder'])){
      // 編集機能
      $sql = 'SELECT * FROM tbPost ORDER BY cnt ASC';
      $stmt = $pdo->query($sql);
      foreach ($stmt as $row) {
        $sql = 'UPDATE tbPost set name=:name,comment=:comment,date=:date,pwd=:pwd WHERE cnt=:id';
        $stmt = $pdo -> prepare($sql);
        $stmt -> bindParam(':id', $edit_id, PDO::PARAM_INT);
        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
        $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
        $stmt -> bindParam(':pwd', $pwd, PDO::PARAM_STR);
        $stmt -> execute();
      }
    }
  }
}

// 削除機能
if(isset($_POST['dlt_btn'])){
  if(!empty($_POST['dlt_num']) && (!empty($_POST['dlt_pwd']))){
    $id = (int)$_POST['dlt_num'];
    $del_pwd = $_POST['dlt_pwd'];
    $sql = 'SELECT * FROM tbPost ORDER BY cnt ASC';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
      if($row['cnt'] == $id){
        if($row['pwd'] == $del_pwd){
          $sql = 'DELETE FROM tbPost where cnt=:id';
          $stmt = $pdo->prepare($sql);
          $stmt->bindParam(':id', $row['cnt'], PDO::PARAM_INT);
          $stmt->execute();
          echo 'Post no.'.$row['cnt'].' has successfully deleted.<hr>';
        }
        // else{echo 'Incorrect password!<hr>';}
      }
    }
  }
}

//表示機能
$sql = 'SELECT * FROM tbPost ORDER BY cnt ASC';
$stmt = $pdo->query($sql);
foreach ($stmt as $row) {
  echo $row[0].' ';
  echo $row[1].' ';
  echo $row[2].' ';
  echo $row[3].' ';
  echo '<br />';
} 
?>
