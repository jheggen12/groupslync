<?php
session_start();
require '../../dbh.php';


if(isset($_SESSION['uid'])) {
  $uid = $_SESSION['uid'];
  //link handling
  if(empty($uid)){
    header("Location: ../index.php");
    exit();
  } else {
    $sql = "DELETE FROM notifications WHERE recipient=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
        echo 'failed to delete';
        exit();
    } else {
        mysqli_stmt_bind_param($stmt,"s", $uid);
        mysqli_stmt_execute($stmt);
    }
    
    exit();
    mysqli_stmt_close($stmt);
  }
} else {
  echo 'Page load failure';
}
