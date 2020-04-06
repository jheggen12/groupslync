<?php
session_start();
require '../../dbh.php';

$postid = $_POST['postid'];
$uid = $_SESSION['uid'];

//link handling
if(empty($postid) || empty($uid) ){
  exit();
}
else {
  $sql = "DELETE FROM groupPostlikes WHERE uid=? AND postid=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"ss", $uid, $postid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "UPDATE groupposts SET likes = likes-1 WHERE id=?";
  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt,$sql)) {
  } else {
    mysqli_stmt_bind_param($stmt,"s",$postid);
    mysqli_stmt_execute($stmt);
  }
  mysqli_stmt_close($stmt);
}
