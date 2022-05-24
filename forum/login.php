<?php
session_start();
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

$user = $_POST['user'];

// データベースに接続
try {
    $pdo = new PDO($dsn, $envId, $envPassword, $option);
    $sql = "SELECT * FROM account WHERE user = :user";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user', $user);
    $stmt->execute();
    $member = $stmt->fetch();



    //指定したハッシュがパスワードにマッチしているかチェック
    if (password_verify($_POST['password'], $member['password'])) {
        //DBのユーザー情報をセッションに保存
        echo $member['user'];
        $_SESSION['view_name'] = $member['user'];
        $_SESSION['login_message'] = '正常にログインしました';
    } else {
        $_SESSION['login_message'] = 'ユーザー名もしくはパスワードが間違っています。';
    }
} catch (PDOException $e) {
    // 接続エラーのときエラー内容を取得する
    $error_message[] = $e->getMessage();
    exit;
} finally {
    $pdo = null;
}
