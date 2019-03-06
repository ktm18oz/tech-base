<html lang = 'ja'>

<head>
  <title>マイページ</title>
  <link rel = "stylesheet" type = "text/css" href = "./../../css/mypage.css">
  <meta charset = "utf-8">
</head>

<body>
  <header>
    <div class="container">
      <a href="./../../index.php"><img src="./../../img/logo_01.png" width="260"></a>
    </div>
  </header>

  <div class="left">
    <h1>マイページ</h1>
  </div>

  <?php
  //セッションスタート
  session_start();
  //DB接続
  require_once("./../config/db.php");
  $dbh = db_connect();

  //セッション関数受け取り
  $_SESSION['user']       = "";
  $_SESSION['my_id']      = $_SESSION['id'];
  $_SESSION['my_account'] = $_SESSION['account'];
  $_SESSION['my_mail']    = $_SESSION['mail'];
  $_SESSION['my_pass']    = $_SESSION['password'];

  //-----テーブルにアイテムを配列として追加していく-----

  //アイテムテーブル作成
  require_once("./../config/tb_memo.php");
  $tb_memo = $_SESSION['my_id']."_".$_SESSION['my_account']."_memolist";
  /*
  $tb_memo = $_SESSION['my_id']."_".$_SESSION['my_account']."_"."memolist";
  ex: 1_account_memolist
  */

  //select関数指定
  function select($dbh, $tb_memo) {
    /*sql文指定
    desc: 降順(5.4.3...)
    asc : 昇順(1.2.3...)
    新しく追加されるものほどid値は大きい(id auto AUTO_INCREMENTだから)
    ORDER BY id DESC : 投稿が新しいものから取得
    SQL文：SELECT * FROM テーブル名 ORDER BY id DESC
    SELECT文とはデータベースのテーブルからデータを検索し、取得するSQLのこと
    構文：SELECT 列名1, 列名2, ・・・ FROM テーブル名 WHERE 条件
    */
    $sql = 'SELECT * FROM '.$tb_memo.' ORDER BY id DESC';
    //DB接続
    require_once("./../config/db.php"); $dbh = db_connect();
    $stmt = $dbh -> query($sql);
    $result = $stmt -> fetchAll(PDO::FETCH_ASSOC);
    return $result;
  }

  //メモの数を返す
  function memo_num($tb_memo) {
    require_once("./../config/db.php"); $dbh = db_connect();
    $sql = 'SELECT * FROM '.$tb_memo.' ORDER BY id DESC';
    $stmt = $dbh -> query($sql);
    $result = $stmt -> fetchAll();
    $z = count($result);
    if($z == "") {
      return "0";
    } else {
      return $z;
    }
  }

  //select関数動作確認用
  function prac_select($dbh,$tb_memo) {

    echo '$resultの中身全部見る<br />';
    echo '<pre>';
    print_r(select($dbh,$tb_memo));
    echo '<pre>';

    echo 'array[0]に入っている配列＝表の行の部分の表示<br />';
    $a = select($dbh,$tb_memo);
    print_r($a[0]['id']);
    print_r($a[0]['memo']);
    print_r($a[0]['category']);
    print_r($a[0]['create_date']);

    echo '$resultの型を見る<br />';
    gettype($a); //array

  }

  //$tb_memoを消す関数
  function delete_table($dbh, $tb_memo) {
    $sql = 'DROP TABLE IF EXISTS '.$tb_memo;
    //DB接続
    require_once("./../config/db.php"); $dbh = db_connect();
    $stmt = $dbh->query($sql);
    $stmt -> execute();
  }
  function click_to_delete_table($dbh, $tb_memo, $tb_dlt_btn) {
    if ( isset($tb_dlt_btn) ) {
      delete_table($dbh, $tb_memo);
      echo 'Table has deleted.<br />';
    }
  }

  //Addされた新規メモを表に入れる
  function table($items) {
    if (!is_array($items)) {
      return FALSE;
    } else {
      $keys1 = array_keys($items);
      $keys2 = array_keys($items[$keys1[0]]);
      $n = count($items[$keys1[0]]); // $n = 5 id,memo,category,deadline,create_dateの5つ

      //テーブル作成文
      $html  = "<table>\n<tr>";
      //1行目(見出し)
      $html .= '<th class = "memo"     >メモ内容</th>';
      $html .= '<th class = "category" >カテゴリ</th>';
      $html .= '<th class = "date"     >締切り日</th>';
      $html .= '<th class = "date"     >作成日時</th>';
      $html .= '<th class = "button"   >編集</th>';
      $html .= '<th class = "button"   >削除</th> </tr><br>';
      //2行目以降(追加されてきたメモたち=配列本体)--------------------------------------
      foreach ($items as $key => $items1){
        $b = $key;
        $_SESSION['num'] = $b;
        $html .= "<tr>";
        /* (id),memo,category,deadline,create_dateを追加
        方法1
        for ($i = 1; $i < $n; $i++) { $html .= "<td>{$items1[$keys2[$i]]}</td>"; }
        */
        //方法2
        // $html .="<td>{$items1[$keys2[0]]}</td>"; //id
        $html .='<td class = "memo"     >'."{$items1[$keys2[1]]}".'</td>'; //memo
        $html .='<td class = "category" >'."{$items1[$keys2[2]]}".'</td>'; //category
        $html .='<td class = "date"     >'."{$items1[$keys2[3]]}".'</td>'; //deadline
        $html .='<td class = "date"     >'."{$items1[$keys2[4]]}".'</td>'; //create_date

        //編集ボタン追加
        $html .= '<td><form method="post">';
        $html .= '<input type = "submit" name = "edt_btn"      value = "編集" class = "button">';
        $html .= '<input type = "hidden" name = "edt_key"      value = '."{$b}".'>'; //key
        $html .= '<input type = "hidden" name = "edt_id"       value = '."{$items1[$keys2[0]]}".'>'; //id
        $html .= '<input type = "hidden" name = "edt_memo"     value = '."{$items1[$keys2[1]]}".'>'; //category
        $html .= '<input type = "hidden" name = "edt_category" value = '."{$items1[$keys2[2]]}".'>'; //deadline
        $html .= '<input type = "hidden" name = "edt_deadline" value = '."{$items1[$keys2[3]]}".'>'; //create_date
        $html .= '</form></td>';

        //編集ボタン追加
        $html .= '<td><form method="post">';
        $html .= '<input type = "submit" name = "dlt_btn"      value = "削除" class = "button">';
        $html .= '<input type = "hidden" name = "dlt_key"      value = '."{$b}".'>'; //key
        $html .= '<input type = "hidden" name = "dlt_id"       value = '."{$items1[$keys2[0]]}".'>'; //id
        $html .= '<input type = "hidden" name = "dlt_memo"     value = '."{$items1[$keys2[1]]}".'>'; //category
        $html .= '<input type = "hidden" name = "dlt_category" value = '."{$items1[$keys2[2]]}".'>'; //deadline
        $html .= '<input type = "hidden" name = "dlt_deadline" value = '."{$items1[$keys2[3]]}".'>'; //create_date
        $html .= '</form></td>';

      }

      //テーブル作成文終了
      $html .= "</tr>\n</table>\n";

      //    return $html;
      echo $html;
    }
  }

  //メモをテーブル状態で表示
  function show_table($dbh, $tb_memo) {
    $select = select($dbh, $tb_memo);
    if(!empty($select)) {
      table($select);
    } else {
      echo 'あたなのメモリストはありません。<br >';
    }
  }

  //ログアウト関数 //ok
  function click_to_logout() {
    session_start();
    session_destroy();
    $_SESSION = array();
    header("Location: ./logout.php");
  }

  //編集機能
  function edit($tb_memo, $memo, $category, $due_date, $create_date, $holder) {
    //DB接続
    require_once("./../config/db.php"); $dbh = db_connect();
    //SQL文
    $sql = 'SELECT * FROM '.$tb_memo.' ORDER BY id DESC';
    $stmt = $dbh -> query($sql);
    foreach ($stmt as $row){
      $sql = 'UPDATE '.$tb_memo.' SET memo=:memo, category=:category, due_date=:due_date, create_date=:create_date WHERE id=:id';
      //require_once("./../config/db.php"); $dbh = db_connect();
      $stmt = $dbh -> prepare($sql);
      $stmt -> bindValue ( ':id',           $holder,      PDO::PARAM_INT );
      $stmt -> bindParam ( ':memo',         $memo,        PDO::PARAM_STR );
      $stmt -> bindParam ( ':category',     $category,    PDO::PARAM_STR );
      $stmt -> bindParam ( ':due_date',     $due_date,    PDO::PARAM_STR );
      $stmt -> bindParam ( ':create_date',  $create_date, PDO::PARAM_STR );
      $stmt -> execute();

    }
  }

  //追加機能
  function add($tb_memo, $memo, $category, $due_date, $create_date) {
    //DB接続
    require_once("./../config/db.php"); $dbh = db_connect();
    //SQL文
    $stmt = $dbh -> prepare('INSERT INTO '.$tb_memo
    .' (id,memo,category,due_date,create_date)'
    .' VALUES'
    .' (:id,:memo,:category,:due_date,:create_date)');
    //AUTO_INCREMENT制約が付いているカラムは0あるいはNULLを設定することで自動的に連番の値を設定することができます。
    $id_auto = 0;
    $stmt -> bindValue ( ':id',           $id_auto,     PDO::PARAM_INT );
    $stmt -> bindParam ( ':memo',         $memo,        PDO::PARAM_STR );
    $stmt -> bindParam ( ':category',     $category,    PDO::PARAM_STR );
    $stmt -> bindParam ( ':due_date',     $due_date,    PDO::PARAM_STR );
    $stmt -> bindParam ( ':create_date',  $create_date, PDO::PARAM_STR );
    $stmt -> execute();

  }

  //削除機能
  function dlt($tb_memo, $dlt_id) {
    //DB接続
    require_once("./../config/db.php"); $dbh = db_connect();
    //SQL文
    $sql = 'SELECT * FROM '.$tb_memo.' ORDER BY id DESC';
    $stmt = $dbh -> query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
      $sql = 'DELETE FROM '.$tb_memo.' WHERE id=:id';
      //require_once("./../config/db.php"); $dbh = db_connect();
      $stmt = $dbh -> prepare($sql);
      $stmt -> bindParam(':id', $dlt_id, PDO::PARAM_INT);
      $stmt -> execute();
    }
  }

  //変数の定義
  $memo         = $_POST['memo'];
  $category     = $_POST['category'];
  $due_date     = $_POST['due_date'];
  $create_date  = date('Y/n/j G:i');

  //ボタン
  $logout_btn   = $_POST['logout_btn'];
  $tb_dlt_btn   = $_POST['tb_dlt_btn'];
  $add_btn      = $_POST['add_btn'];

  //hidden
  $holder       = $_POST['holder'];

  //編集POST
  $edt_btn      = $_POST['edt_btn'];
  $edt_key      = $_POST['edt_key'];
  $edt_id       = $_POST['edt_id'];
  $edt_memo     = $_POST['edt_memo'];
  $edt_category = $_POST['edt_category'];
  $edt_deadline = $_POST['edt_deadline'];

  //削除POST
  $dlt_btn      = $_POST['dlt_btn'];
  $dlt_key      = $_POST['dlt_key'];
  $dlt_id       = $_POST['dlt_id'];
  $dlt_memo     = $_POST['dlt_memo'];
  $dlt_category = $_POST['dlt_category'];
  $dlt_deadline = $_POST['dlt_deadline'];

  //追加＋編集機能
  if( isset ($add_btn) ) {
    if( $holder == "" ) {
      $z = memo_num($tb_memo);
      if($z < 15 ){
        //追加機能
        add($tb_memo, $memo, $category, $due_date, $create_date);
      }else{
        $alert  = "<script type='text/javascript'>";
        $alert .= "alert('これ以上メモを追加することはできません。\n";
        $alert .= "新しくメモを作る場合は既存のメモを削除してください。');</script>";
        echo $alert;
      }
    } elseif( $holder != "" ) {
      //編集機能
      edit($tb_memo, $memo, $category, $due_date, $create_date, $holder);
    }
  }

  //削除機能
  if( isset ($dlt_btn) ) {
    dlt($tb_memo, $dlt_id);
  }

  ?>


  <div class="right">
    Account name : <?= $_SESSION['my_account'];?><br />
    Mail address 　: <?= $_SESSION['my_mail'];?>
  </div>

  <main class="container">


    <!-- ログアウト -->
    <form method="post"><input id='logout_btn' type="submit" name="logout_btn" value="Logout"></form>
    <?php if( isset ($logout_btn) ) { click_to_logout(); } ?><!-- <br> -->

    <div>
      <form action="" method="post">
        カテゴリ：
        <input id='category' type='text' name='category' placeholder='カテゴリー'
        value='<?php
        if( ! isset ($edt_btn) ) {
        } else {
          echo $edt_category;
        }
        ?>' required><br />

        締切り日：
        <input id='category' type='date' name='due_date' placeholder='締め切り日'
        value='<?php
        if( ! isset ($edt_btn) ) {
        } else {
          echo $edt_deadline;
        }
        ?>' required><br />

        メモ内容：
        <input id='textbox'  type='text' name='memo'     placeholder='内容'
        value='<?php
        if( ! isset ($edt_btn) ) {
        } else {
          echo $edt_memo;
        }
        ?>' required><br />
        メモの数：
        <?= $num = !isset($tb_dlt_btn) ? memo_num($tb_memo) : "0"; ?>　(メモは15個まで保存できます)
        <!-- holder -->
        <input id='edit_holder'          type='hidden' name='holder'
        value='<?php
        if( ! isset ($edt_btn) ) {
          echo "";
        } else {
          echo $edt_id;
        }
        ?>'>

        <input id='add_btn'  class='btn' type='submit' name='add_btn'
        value="<?php
        if( ! isset ($edt_btn) ) {
          echo "追加する";
        } else {
          echo "編集する";
        }
        ?>">

      </form>
    </div>

    <hr>

    <!-- tb_memo削除 -->

    <script>
    /**
    * 確認ダイアログの返り値によりフォーム送信
    */
    function submitChk () {
      /* 確認ダイアログ表示 */
      var flag = confirm ( "本当に全てのメモを削除しますか？");
      /* send_flg が TRUEなら送信、FALSEなら送信しない */
      return flag;
    }
    </script>

    <form class="right_btn" method='post' onsubmit="return submitChk()"><input type='submit' name ='tb_dlt_btn' value='全て削除する'></form>
    <?php
    if( isset ($tb_dlt_btn) ) {
      delete_table($dbh, $tb_memo);
      require_once("./../config/tb_memo.php");
      echo "全てのメモが削除されました。";
    } else { show_table($dbh, $tb_memo); memo_num($tb_memo);
    } ?>

  </main>

  <footer>
    <small>(c)2019 Katsumi Nagaike ALL RIGHTS RESERVED.</small>
  </footer>


</body>
</html>
