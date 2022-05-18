<?php
// envファイルの読み込み
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

//.envから
$envDbname = $_ENV['DB_NAME'];
$envHost = $_ENV['HOST'];
$envId = $_ENV['ID'];
$envPassword = $_ENV['PASSWORD'];
$dsn = "mysql:charset=UTF8;dbname=$envDbname;host=$envHost";

// データベースに接続
try {
    $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    );
    $pdo = new PDO($dsn, $envId, $envPassword);
} catch (PDOException $e) {
    // 接続エラーのときエラー内容を取得する
    $error_message[] = $e->getMessage();
    exit;
}

$pdo->beginTransaction();

$user = $_POST['user'];
$password = $_POST['password'];

$stmt = $pdo->prepare('INSERT INTO account (user, password) VALUES (:user, :password)');

$stmt->bindParam(':user', $user);
$stmt->bindParam(':password', $password);

$stmt->execute();
$pdo->commit();

$pdo = null;
$stmt = null;