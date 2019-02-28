<?php
  //MySQLに接続する(3-1)
  $dsn = 'database_name';
  $user = 'user_name';
  $password = 'password';
  $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
?>

<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>mission_4</title>
  </head>
  <body>
    <h1>簡易掲示板</h1>
    <h5><form method="post">
      <!-- 投稿フォーム -->
      <input type="text" name="name" placeholder="Name"value="alex" required><br />
      <!-- value="</?php
        if(isset($_POST["edit_btn"])&&$_POST["edit_num"]!=""){
          if(isset($_POST["edit_btn"])){
            if(file_exists()){ $txt_rows = file(); }
            for($i=0; $i<count($txt_rows); $i++){
              $elem = explode("<>", $txt_rows[$i]);
              if($elem[0]==$_POST["edit_num"]){
                if($elem[4] == $_POST["pwd_edit"]){
                  echo  $elem[1];
                }
              }
            }
          }
         }//else{echo "Mr.A";}
        ?>"><br />-->
      <input type="text" name="cmnt" placeholder="Comment" value="new comment"><br />
      <input type="text" name="pwd" placeholder="set your password" value="pwd" required>
      <input type="submit" name ="sbmt_btn" value="Submit">
    </form></h5>

    <!-- 削除フォーム -->
    <h5><form method="post">
      <input type="text" name="dlt_num" placeholder="delete number"><br />
      <input type="text" name="pwd_dlt" placeholder="password required" value="pwd" required>
      <input type="submit" name ="dlt_btn" value="Delete">
    </form></h5>

  </body>
</html>

<?php
  //投稿のデータを格納するテーブルを作る(3-2)
  $sql = "CREATE TABLE IF NOT EXISTS tbPost"
  ."("
  ."cnt INT NOT NULL AUTO_INCREMENT primary key,"
  ."name varchar(32),"
  ."comment TEXT,"
  ."date TEXT,"
  ."pwd TEXT"
  .");";
  $stmt = $pdo->query($sql);

  //テーブル一覧を表示(3-3)
  // $sql_show = "SHOW TABLES";
  // $result = $pdo->query($sql_show);//object(PDOStatement)+array type
  // foreach ($result as $row){
  //   var_dump($row[0]); echo "<br>";
  // }//確認用：最後は消す

  //意図した内容のテーブルが作成されているか確認する(3-4)
  // $sql ="SHOW CREATE TABLE tbPost";
  // $result = $pdo -> query($sql);
  // foreach ($result as $row){
  //   for($i=0;$i<count($row);$i++){
  //     echo $row[$i];
  //   }
  // }
  // echo "<hr>";

  //tbPostにデータを挿入する(3-5)
  if(isset($_POST["sbmt_btn"])){
    //echo "pressed<br>";//確認用
    if(!empty($_POST["cmnt"]) && (!empty($_POST["name"]))){
      $name = $_POST["name"];
      $comment = $_POST["cmnt"];
      $date = date("Y/n/j G:i:s");
      $pwd = $_POST["pwd"];

      $stmt = $pdo -> prepare("INSERT INTO tbPost (cnt,name,comment,date,pwd) VALUES (:cnt,:name,:comment,:date,:pwd)");
      $stmt -> bindParam(":cnt", $cnt, PDO::PARAM_INT);
      $stmt -> bindParam(":name", $name, PDO::PARAM_STR);
      $stmt -> bindParam(":comment", $comment, PDO::PARAM_STR);
      $stmt -> bindParam(":date", $date, PDO::PARAM_STR);
      $stmt -> bindParam(":pwd", $pwd, PDO::PARAM_STR);
      $stmt -> execute(); //excuteメソッドでクエリの実行
    }
  }
//  echo "<hr>";

// 削除機能
if(isset($_POST["dlt_btn"])){
  if(!empty($_POST["dlt_num"]) && (!empty($_POST['pwd_dlt']))){
    $id = $_POST["dlt_num"];
    $del_pwd = $_POST['pwd_dlt'];
    $sql = "SELECT * FROM tbPost ORDER BY cnt ASC";
    $stmt = $pdo->query($sql);
    foreach ($stmt as $row) {
      if($row[4] == $del_pwd){

        $sql = "DELETE FROM tbPost WHERE cnt = :id";
        $stmt = $pdo->prepare($sql);
        $params = array(':id'=>$id);
        $stmt->execute($params);
      }
    }
  }
}

//表示機能
  $sql = "SELECT * FROM tbPost ORDER BY cnt ASC";
  $stmt = $pdo->query($sql);
  foreach ($stmt as $row) {
    echo $row[0]." ";
    echo $row[1]." ";
    echo $row[2]." ";
    echo $row[3]." ";
    //echo $row[4];//pwd
    echo "<br>";
  }
?>
