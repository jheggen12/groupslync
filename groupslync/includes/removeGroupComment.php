<?php
session_start();
require '../../dbh.php';

if(isset($_SESSION['uid'])) {
  $commentid = $_POST['commentid'];
  $uid = $_SESSION['uid'];

  if(empty($uid) || empty($commentid)){
    exit();
  }
  else {
    $sql = "DELETE FROM groupcomments WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s", $commentid);
      mysqli_stmt_execute($stmt);
      exit();
    }
    mysqli_stmt_close($stmt);
  }
} else {
  echo 'Page load failure';
}
