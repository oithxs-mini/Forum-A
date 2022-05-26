<?php
//function php_func(){
//    echo "<script>console.log(1)</script>";
//}


$pdo = new PDO(
    'mysql:host=localhost;dbname=board', 'root', 'pass'
);
// SQL文をセット
$stmt = $pdo->prepare('SELECT * FROM message WHERE id = :id');

$stmt->bindValue(":id", $p);

$stmt->execute();

// ループして1レコードずつ取得
$stmt->fetch();
foreach ($stmt as $row) {
    echo $row->good;
    //ここ
}


?>

<button onclick="js_func()" class="btn btn-danger">いいね！</button>
<var> x </var>
<!-- ここ-->

<div style="display: none;"></div>