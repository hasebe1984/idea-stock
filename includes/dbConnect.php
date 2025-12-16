<?php
// データベースの情報を定数化
define('DB_HOST', 'localhost');
define('DB_NAME', 'ideastock');
define('DB_USER', 'root');
define('DB_PASS', 'pass-a');
define('DB_CHARSET', 'utf8');

$dsn = "mysql:host=". DB_HOST. ";dbname=". DB_NAME. ";charset=". DB_CHARSET;

// 接続を試みる
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS);                         //データベース接続
    $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);   //エラーが発生した瞬間に処理を中断。エラーの内容が格納された(PDOException)をcatchに渡す

// 接続が失敗した時
} catch (PDOException $e) {                           //PDOExceptionを$eに渡す
    echo "データベース接続エラー:". $e->getMessage();   //PDOExceptionの詳細（getMessage）を表示
    exit();                                           //処理終了
}
?>