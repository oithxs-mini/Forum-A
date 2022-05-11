<?php
// envファイルの読み込み
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$view_name = null;
$message = array();
$message_data = null;
$error_message = array();
$pdo = null;
$stmt = null;
$res = null;
$option = null;

//.envから
$envDbname = $_ENV['DB_NAME'];
$envHost = $_ENV['HOST'];
$envId = $_ENV['ID'];
$envPassword = $_ENV['PASSWORD'];
$dsn = "mysql:charset=UTF8;dbname=$envDbname;host=$envHost";

session_start();

// データベースに接続
try {
    $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
    );
    $pdo = new PDO($dsn, $envId, $envPassword, $option);
} catch (PDOException $e) {
    // 接続エラーのときエラー内容を取得する
    $error_message[] = $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ひとこと掲示板</title>
    <link rel="stylesheet" href="../css/index.css" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Train+One&display=swap" rel="stylesheet" />
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="/" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <span class="title h1">HxS掲示板</span>
                </a>

                <ul class="nav col-12 col-lg-auto me-lg-auto mb-3 justify-content-center mb-md-0">
                    <li>
                        <a href="/" class="nav-link px-2 text-white">Home</a>
                    </li>
                    <li>
                        <a href="./" class="nav-link px-2 text-secondary">Forum</a>
                    </li>
                    <li>
                        <a href="../about/" class="nav-link px-2 text-white">About</a>
                    </li>
                </ul>

                <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
                    <input type="search" class="form-control form-control-dark" placeholder="Search..." aria-label="Search" />
                </form>

                <div class="text-end">
                    <button type="button" class="btn btn-outline-light me-2">
                        Login
                    </button>
                    <button type="button" class="btn btn-warning">Sign-up</button>
                </div>
            </div>
        </div>
    </header>

    <div class="warapper">



    </div>

</body>