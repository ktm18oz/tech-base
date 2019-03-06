<?php
  //データベース接続
  function db_connect(){
    $dsn = 'dbname';
    $user = 'user';
    $password = 'pass';

    try{

      //例外が発生するおそれがあるコード
      $dbh = new PDO($dsn,
                     $user,
                     $password,

                     /* ↓option は連想配列で指定 */
                     array(
                       PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
                       /*
                       ↓静的プレースホルダを使うために、オプションでエミュレートする機能をOFFにする
                       (動的プレースホルダを使うと、SQLインジェクションを許してしまう可能性がある)
                       */
                       PDO::ATTR_EMULATE_PREPARES => false,
                     )
                   );

      return $dbh;

    } /*
          ↓例外処理 catch(例外クラス名　例外を受け取る変数名){処理}
          PDOクラスのコンストラクタ「PDO::__construct」には、下記のようにPDOExceptioonを投げると書いてある
         「PDO::__construct() は、 指定されたデータベースへの接続に失敗した場合、 PDOException を投げます。」
      */
      catch (PDOException $e){
      print('Error:'.$e->getMessage());

      // ↓die('メッセージ')：メッセージを出力し、現在のスクリプトを終了する
      die();
    }
  }

?>
