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

if (isset($_POST['seword'])) {
    $_SESSION['seword'] = $_POST['seword'];
}
//ログアウト
if (isset($_POST['logout'])) {
    $_SESSION = array(); //セッションの中身をすべて削除
    session_destroy(); //セッションを破壊
    $login_message = '正常にログアウトしました';
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

if (!empty($_POST['btn_submit'])) {

    // 空白除去
    $view_name = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $_POST['view_name']);
    $message = preg_replace('/\A[\p{C}\p{Z}]++|[\p{C}\p{Z}]++\z/u', '', $_POST['message']);

    // 表示名の入力チェック
    if (empty($view_name)) {
    } else {
        // セッションに表示名を保存
        $_SESSION['view_name'] = $view_name;
    }

    // メッセージの入力チェック
    if (empty($message)) {
        $error_message[] = 'ひと言メッセージを入力してください。';
    } else {

        // 文字数を確認
        if (100 < mb_strlen($message, 'UTF-8')) {
            $error_message[] = 'メッセージは100文字以内で入力してください';
        }
    }

    if (empty($error_message)) {
        // 書き込み日時を取得
        $current_date = date("Y-m-d H:i:s");

        // トランザクション開始
        $pdo->beginTransaction();

        try {

            // SQL作成
            $stmt = $pdo->prepare("INSERT INTO message (view_name, message, post_date) VALUES ( :view_name, :message, :current_date)");

            // 値をセット
            $stmt->bindParam(':view_name', $view_name, PDO::PARAM_STR);
            $stmt->bindParam(':message', $message, PDO::PARAM_STR);
            $stmt->bindParam(':current_date', $current_date, PDO::PARAM_STR);

            // SQLクエリの実行
            $res = $stmt->execute();
            // コミット
            $res = $pdo->commit();
        } catch (Exception $e) {

            // エラーが発生した時はロールバック
            $pdo->rollBack();
        }

        if ($res) {
            $_SESSION['success_message'] = 'メッセージを書き込みました';
        } else {
            $error_message[] = '書き込みに失敗しました';
        }

        // プリペアドステートメントを削除
        $stmt = null;

        header('Location: ./');
        exit;
    }
}

if (!empty($pdo)) {

    // メッセージのデータを取得する
    $sql = "SELECT view_name,message,post_date FROM message ORDER BY post_date DESC";
    $message_array = $pdo->query($sql);

    //投稿数を $count に入れる
    $count = $message_array->rowCount();
}

// データベースの接続を閉じる
$pdo = null;

if (isset($_SESSION['view_name']) && $_SESSION['view_name'] == 'admin') {
    header('Location: ./admin.php');
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
                <a href="../" class="d-flex align-items-center mb-2 mb-lg-0 text-white text-decoration-none">
                    <span class="title h1">HxS掲示板</span>
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
                        <form id="loginform" method="POST">
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
                <form id="signupform" method="POST">
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

    <!-- アラート -->
    <?php if (empty($_POST['btn_submit']) && !empty($_SESSION['success_message'])) : ?>
        <div class="alert alert-success d-flex align-items-center container mt-4" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                <use xlink:href="#check-circle-fill" />
            </svg>
            <div>
                <strong>メッセージを書き込みました</strong>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php elseif (!empty($error_message)) : ?>
        <div class="alert alert-warning d-flex align-items-center container mt-4" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24">
                <use xlink:href="#exclamation-triangle-fill" />
            </svg>
            <use xlink:href="#check-circle-fill" />
            </svg>
            <div>
                <?php foreach ($error_message as $value) ?>
                <strong><?php print $value; ?></strong>
            </div>
        </div>
    <?php elseif (!empty($login_message)) : ?>
        <div class="alert alert-primary d-flex align-items-center container mt-4" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="情報:">
                <use xlink:href="#info-fill" />
            </svg>
            <div>
                <strong><?php print $login_message; ?></strong>
            </div>
        </div>
    <?php elseif (!empty($_SESSION['login_message'])) : ?>
        <div class="alert alert-primary d-flex align-items-center container mt-4" role="alert">
            <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="情報:">
                <use xlink:href="#info-fill" />
            </svg>
            <div>
                <strong><?php print $_SESSION['login_message']; ?></strong>
            </div>
        </div>
        <?php unset($_SESSION['login_message']); ?>
    <?php endif; ?>

    <div class="container mt-5">
        <form method="post" id="formmessage">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">表示名</label>
                <input type="text" name="view_name" class="form-control messagearea" id="username" value="<?php if (!empty($_SESSION['view_name'])) {
                                                                                                                echo htmlspecialchars($_SESSION['view_name'], ENT_QUOTES, 'UTF-8');
                                                                                                            } else {
                                                                                                                echo '匿名';
                                                                                                            } ?>" readonly />
            </div>
            <div class="mb-3">
                <label for="exampleFormControlTextarea1" class="form-label">メッセージ</label>
                <textarea name="message" class="form-control messagearea" id="textarea" rows="4"><?php if (!empty($message)) {
                                                                                                        echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
                                                                                                    } ?></textarea>
            </div>
            <div class="d-grid gap-2 col-6 mx-auto">
                <button type="submit" name="btn_submit" class="btn btn-primary" id="sendbtn" value="書き込む" disabled>
                    書き込む
                </button>
            </div>
        </form>
    </div>

    <hr>

    <div class="container">
        <div class="d-flex flex-row-reverse bd-highlight">
            <div class="col-auto">
                <input type="text" class="form-control" id="serchcontent" placeholder="<?php if (isset($_SESSION['seword'])) {
                                                                                            echo $_SESSION['seword'];
                                                                                        } else {
                                                                                            echo "投稿内容を検索";
                                                                                        } ?>">

            </div>
        </div>
    </div>

    <div class="container my-5">
        <section>
            <?php if (!empty($message_array)) { ?>
                <?php foreach ($message_array as $value) { ?>
                    <?php if (isset($_SESSION['seword'])) {
                        if (strpos($value['message'], $_SESSION['seword']) !== false) { ?>
                            <article class="alert-secondary">
                                <div class="info">
                                    <h2><?php echo htmlspecialchars($value['view_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                    <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                                </div>
                                <p><?php echo nl2br(htmlspecialchars($value['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                            </article>
                        <?php } else {
                            $count--;
                        }
                    } else { ?>
                        <article class="alert-secondary">
                            <div class="info">
                                <h2><?php echo htmlspecialchars($value['view_name'], ENT_QUOTES, 'UTF-8'); ?></h2>
                                <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($value['message'], ENT_QUOTES, 'UTF-8')); ?></p>
                        </article>
                    <?php }
                    if ($count == 0) { ?>
                        <div class="h1 text-center">検索に該当する投稿はありません</div>
            <?php }
                }
            } ?>
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