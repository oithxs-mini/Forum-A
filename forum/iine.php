<?php
#create :shirase
#website:fsbblog.jp
#date   :2021/09/26
session_start();
if($_SERVER['REQUEST_METHOD'] == "POST"){
 if(isset($_SESSION["test_iine_token"]) && isset($_SESSION["test_entry_id"]) && isset($_POST["test_iine_token"])){
  if($_SESSION["test_iine_token"] === $_POST["test_iine_token"]){
   $result = false;
   $iine_path = "/iine_counter".$_SESSION["test_entry_id"];
   if(!file_exists($iine_path)){
    file_put_contents($iine_path,"1");
   }else{
    $fp = fopen($iine_path,'r+');
    if($fp && flock($fp, LOCK_EX)){
     $counter = fgets($fp)+1;
     rewind($fp);
     ftruncate($fp, 0);
     fputs($fp, $counter);
     fclose($fp);
     $result = true;
    }
   }
   $_SESSION["test_iine_token"] = null;
   $_SESSION["test_entry_id"] = null;
   echo $result;
  }
 }else{
  header("Location: /");
  exit;
 }
}else{
 header("Location: /");
 exit;
}
?>