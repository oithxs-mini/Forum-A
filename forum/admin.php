<?php
// envファイルの読み込み
require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 変数の初期化
$current_date = null;
$message = array();
$message_array = array();
$success_message = null;
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

//ログアウト
if (isset($_POST['logout'])) {
    $_SESSION = array(); //セッションの中身をすべて削除
    session_destroy(); //セッションを破壊
    $login_message = '正常にログアウトしました';
    header('Location: ./');
}

if (!empty($_GET['btn_logout'])) {
    unset($_SESSION['admin_login']);
}

// データベースに接続
try {
    $option = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS =>
        false,
    );
    $pdo = new PDO($dsn, $envId, $envPassword, $option);
} catch (PDOException $e) {
    // 接続エラーのときエラー内容を取得する
    $error_message[] = $e->getMessage();
}

if (!empty($pdo)) {

    // メッセージのデータを取得する
    $sql = "SELECT * FROM message ORDER BY post_date DESC";
    $message_array = $pdo->query($sql);
}

// データベースの接続を閉じる
$pdo = null;

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ひとこと掲示板 管理ページ</title>
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
                <a href="../" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <span class="title h1">掲示板</span>
                </a>

                <ul class="nav col-12 col-lg-auto me-lg-auto mb-3 justify-content-center mb-md-0">
                    <li>
                        <a href="../" class="nav-link px-2 text-white">Home</a>
                    </li>
                    <li>
                        <a href="./" class="nav-link px-2 text-secondary">Forum</a>
                    </li>
                    <li>
                        <a href="../status/" class="nav-link px-2 text-white">Status</a>
                    </li>
                </ul>

                <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
                    <input type="search" class="form-control form-control-dark" placeholder="Search..." aria-label="Search" />
                </form>

                <?php if (empty($_SESSION['view_name']) || $_SESSION['view_name'] == "匿名") : ?>

                    <div class="text-end">
                        <button type="button" class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-whatever="@mdo">Login</button>
                        <form id="loginform">
                            <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog ">
                                    <div class="modal-content bg-light">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-dark" id="exampleModalLabel">ログイン</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label for="recipient-name" class="col-form-label text-dark">ユーザー名</label>
                                                <input type="text" class="form-control liarea" id="liuser" name="user">
                                            </div>
                                            <div class="mb-3">
                                                <label for="message-text" class="col-form-label text-dark">パスワード</label>
                                                <input type="text" class="form-control liarea" id="lipass" name="password">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                                            <button type="submit" name="login" class="btn btn-primary" id="loginbtn" disabled>ログイン</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                <?php else : ?>

                    <div class="text-end">
                        <form method="post">
                            <button type="submit" name="logout" id="logoutbtn" class="btn btn-outline-danger me-2" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-whatever="@mdo">Logout
                            </button>
                        </form>
                    </div>

                <?php endif; ?>

                <button type="button" class="btn btn-warning me-2" data-bs-toggle="modal" data-bs-target="#signupModal" data-bs-whatever="@mdo">Sign-up</button>
                <form id="signupform">
                    <div class="modal fade" id="signupModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog ">
                            <div class="modal-content bg-light">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark" id="exampleModalLabel">サインアップ</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label for="recipient-name" class="col-form-label text-dark">ユーザー名</label>
                                        <input type="text" class="form-control suarea" id="suuser" name="user">
                                    </div>
                                    <div class="mb-3">
                                        <label for="message-text" class="col-form-label text-dark">パスワード</label>
                                        <input type="text" class="form-control suarea" id="supass" name="password">
                                    </div>
                                    <div class="mb-3">
                                        <label for="message-text" class="col-form-label text-dark">パスワードの確認</label>
                                        <input type="text" class="form-control suarea" id="supass2" name="passwords">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                                    <button type="submit" class="btn btn-primary" id="signupbtn" disabled>サインアップ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <p class="text-center h1 mt-4">管理ページ</p>

    <?php if (!empty($error_message[0])) : ?>
        </div>
        <div class="alert alert-danger d-flex align-items-center container mt-4" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                <use xlink:href="#exclamation-triangle-fill" />
            </svg>
            <use xlink:href="#check-circle-fill" />
            </svg>
            <div>
                ログインに失敗しました
            </div>
        </div>
    <?php endif; ?>

    <div class="container my-5">
        <section>
            <?php if (!empty($_SESSION['view_name']) && $_SESSION['view_name'] == 'admin') : ?>

                <form method="get" action="./download.php" class="row gy-2 gx-3 align-items-center">

                    <button type="submit" name="btn_download" class="btn btn-primary rounded-pill col-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                            <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z" />
                            <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z" />
                        </svg> Download </button>
                    <span class="col-auto">
                        <select name="limit" class="form-select">
                            <option value="">全て</option>
                            <option value="10">10件</option>
                            <option value="30">30件</option>
                        </select></span>
                </form>
                <?php if (!empty($message_array)) : ?>
                    <?php foreach ($message_array as $value) : ?>
                        <article class="alert-secondary">
                            <div class="info">
                                <h2><?php echo htmlspecialchars($value['view_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                                <span id="editbtn">
                                    <a class="mx-2" href="edit.php?message_id=<?php echo $value['id']; ?>">編集</a>
                                    <a href="delete.php?message_id=<?php echo $value['id']; ?>">削除</a>
                                </span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($value['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php else : ?>
                <div class="text-center h2">ログインしてください</div>

            <?php endif; ?>
        </section>
    </div>

    <!-- svgの読み込み -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <!-- JSの読み込み -->
    <script src="../js/index.js"></script>
</body>

</html>