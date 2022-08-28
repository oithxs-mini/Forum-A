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

// 管理者としてログインしているか確認
if (empty($_SESSION['view_name']) || $_SESSION['view_name'] != 'admin') {

    // ログインページへリダイレクト
    header("Location: ./admin.php");
    exit;
}

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

if (!empty($_GET['message_id']) && empty($_POST['message_id'])) {

    // SQL作成
    $stmt = $pdo->prepare("SELECT * FROM message WHERE id = :id");

    // 値をセット
    $stmt->bindValue(':id', $_GET['message_id'], PDO::PARAM_INT);

    // SQLクエリの実行
    $stmt->execute();

    // 表示するデータを取得
    $message_data = $stmt->fetch();

    // 投稿データが取得できないときは管理ページに戻る
    if (empty($message_data)) {
        header("Location: ./admin.php");
        exit;
    }
} elseif (!empty($_POST['message_id'])) {

    // 空白除去
    $view_name = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $_POST['view_name']);
    $message = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $_POST['message']);

    // 表示名の入力チェック
    if (empty($view_name)) {
        $error_message[] = '表示名を入力してください。';
    }

    // メッセージの入力チェック
    if (empty($message)) {
        $error_message[] = 'メッセージを入力してください。';
    } else {

        // 文字数を確認
        if (100 < mb_strlen($message, 'UTF-8')) {
            $error_message[] = 'word-count';
        }
    }

    if (empty($error_message)) {

        // トランザクション開始
        $pdo->beginTransaction();

        try {

            // SQL作成
            $stmt = $pdo->prepare("UPDATE message SET view_name = :view_name, message= :message WHERE id = :id");

            // 値をセット
            $stmt->bindParam(':view_name', $view_name, PDO::PARAM_STR);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            $stmt->bindValue(':id', $_POST['message_id'], PDO::PARAM_INT);

            // SQLクエリの実行
            $stmt->execute();

            // コミット
            $res = $pdo->commit();
        } catch (Exception $e) {

            // エラーが発生した時はロールバック
            $pdo->rollBack();
        }

        // 更新に成功したら一覧に戻る
        if ($res) {
            header("Location: ./admin.php");
            exit;
        }
    }
}

// データベースの接続を閉じる
$stmt = null;
$pdo = null;

?>

<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>管理ページ (投稿の編集)</title>
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
                    <span class="title h1">掲示板</span>
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
            </div>
        </div>
    </header>

    <p class="text-center h1 mt-4">管理ページ (投稿の編集)</p>

    <!-- アラート -->
    <?php if (!empty($error_message[0]) && $error_message[0] == 'word-count') : ?>
        </div>
        <div class="alert alert-warning d-flex align-items-center container mt-4" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                <use xlink:href="#exclamation-triangle-fill" />
            </svg>
            <use xlink:href="#check-circle-fill" />
            </svg>
            <div>
                メッセージは100文字以内で入力してください
            </div>
        </div>
    <?php endif; ?>

    <div class="container mt-5">
        <form method="post" id="formmessage">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">表示名</label>
                <input type="text" name="view_name" class="form-control messagearea" id="username" value="<?php if (!empty($message_data['view_name'])) {
                                                                                                                echo $message_data['view_name'];
                                                                                                            } elseif (!empty($view_name)) {
                                                                                                                echo htmlspecialchars($view_name, ENT_QUOTES, 'UTF-8');
                                                                                                            } ?>" />
            </div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">メッセージ</label>
                <textarea name="message" class="form-control messagearea" id="textarea" rows="4"><?php if (!empty($message_data['message'])) {
                                                                                                        echo $message_data['message'];
                                                                                                    } elseif (!empty($message)) {
                                                                                                        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
                                                                                                    } ?></textarea>
            </div>

            <div class="btn-toolbar mt-5">
                <button class="col-1 btn btn-outline-secondary" type="button" onclick="location.href='admin.php'">キャンセル</button>
                <button type="submit" name="btn_submit" class="btn btn-primary col-6 mx-auto" id="sendbtn" value="書き込む" disabled>
                    更新
                </button>
            </div>
            <input type="hidden" name="message_id" value="<?php if (!empty($message_data['id'])) {
                                                                echo $message_data['id'];
                                                            } elseif (!empty($_POST['message_id'])) {
                                                                echo htmlspecialchars($_POST['message_id'], ENT_QUOTES, 'UTF-8');
                                                            } ?>">
        </form>
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