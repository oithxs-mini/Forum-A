<?php
//function php_func(){
//    echo "<script>console.log(1)</script>";
//}



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

//データベース接続
try{

    $dbh = new PDO($dsn, $envId, $envPassword, $option);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SELECT COUNT(;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    echo '接続に成功しました';

}catch (PDOException $e) {
    print($e->getMessage());
    die();
}



/*$pdo = new PDO(
    'mysql:host=localhost;dbname=board', 'root', 'pass'
); */

/*// SQL文をセット
$stmt = $pdo->prepare('SELECT * FROM message WHERE id = :id');

$stmt->bindValue(":id", $p);

$stmt->execute(); 

// ループして1レコードずつ取得
$stmt->fetch();
foreach ($stmt as $row) {
    echo $row->good;
    //ここ
}
*/

?>

<button onclick="js_func()" class="btn btn-danger">いいね！</button>
<var> x </var>
<!-- ここ-->

<div style="display: none;"></div>