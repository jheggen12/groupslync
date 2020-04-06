<?php
session_start();
require '../../dbh.php';

if(isset($_SESSION['uid'])) {
  $postid = $_POST['postid'];
  $uid = $_SESSION['uid'];

  if(empty($postid)){
    exit();
  } else {
    $sql = "DELETE FROM publicposts WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s", $postid);
      mysqli_stmt_execute($stmt);
      $sql = "DELETE FROM publiccomments WHERE postid=?";
      $stmt = mysqli_stmt_init($conn);
      if (!mysqli_stmt_prepare($stmt,$sql)) {
        exit();
      } else {
        mysqli_stmt_bind_param($stmt,"s", $postid);
        mysqli_stmt_execute($stmt);
        $sql = "DELETE FROM publicPostlikes WHERE postid=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql)) {
          exit();
        } else {
          mysqli_stmt_bind_param($stmt,"s", $postid);
          mysqli_stmt_execute($stmt);
          exit();
        }
      }
    }
  }

  mysqli_stmt_close($stmt);
} else {
  exit();
}
