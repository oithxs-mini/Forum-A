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

$user = $_POST['user'];
$password = password_hash(htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT);

// データベースに接続
try {
    $pdo = new PDO($dsn, $envId, $envPassword);
    $sql = "SELECT * FROM account WHERE user = :user";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user', $user);
    $stmt->execute();
    $member = $stmt->fetch();

    if (!empty($member['user'])) {
        $_SESSION['error_message'] = "ユーザー名: $user はすでに存在します\n再登録してください";
    } else {
        $sql = "INSERT INTO account(user,password) VALUES (:user, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':user', $user);
        $stmt->bindValue(':password', $password);
        $stmt->execute();
        $msg = '会員登録が完了しました';
    }
} catch (PDOException $e) {
    // 接続エラーのときエラー内容を取得する
    $error_message[] = $e->getMessage();
    exit;
} finally {
    header('Location: ./index.php', $error_message);
    $pdo = null;
}