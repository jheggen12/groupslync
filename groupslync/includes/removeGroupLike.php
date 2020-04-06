<?php
session_start();
require '../../dbh.php';

$groupid = $_POST['groupid'];
$uid = $_SESSION['uid'];

//link handling
if(empty($groupid) || empty($uid)){
  echo 'ERROR-Unlike failed';
  exit();
}
else {
  $sql = "DELETE FROM grouplikes WHERE uid=? AND groupid=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    echo 'ERROR-Unlike failed';
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"ss", $uid, $groupid);
    mysqli_stmt_execute($stmt);
    $sql = "UPDATE groups SET likecount=likecount-1 WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s", $groupid);
      mysqli_stmt_execute($stmt);
      exit();
    }
  }
  mysqli_stmt_close($stmt);
}
