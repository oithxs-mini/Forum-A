<?php
#create :shirase
#website:fsbblog.jp
#date   :2021/09/26
//session_start();
$contents_number = "SELECT * FROM message ORDER BY id DESC";
if($_SERVER['REQUEST_METHOD'] == "GET"){
 $datetime = (new Datetime(date("Y/m/d H:i:s")))->format('YmdHis');
 $_SESSION["test_iine_token"] = hash('sha256',($datetime.$contents_number));
 $_SESSION["test_entry_id"] = $contents_number;
}
?>


<html>
<head>
<script src="../js/jquery-3.6.0.min.js"></script>
<script src="../js/reaction.js"></script>
</head>

<body>
<?php
$iine_path = "/iine_counter".$contents_number;
$iine_count = 0;
if(!file_exists($iine_path)){
 $iine_count = 0;
}else{
 $fp = fopen($iine_path,'r+');
 if($fp && flock($fp, LOCK_EX)){
  $counter = fgets($fp);
  fclose($fp);
  $iine_count = $counter;
 }
}
echo <<< EOL
<form id="iine_form" method="POST" onClick="iine_reaction();return false;">
<button id="iine_button" type="submit">
<img src="/img/iine.png" style="width:16px;"> いいね！<span id="iine_count">{$iine_count}</span>
</button>
 <input type="hidden" name="test_iine_token" value="{$_SESSION["test_iine_token"]}">
</form>
EOL;
?>

</body>
</html>