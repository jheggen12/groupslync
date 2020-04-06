<?php
session_start();
require '../../dbh.php';

if(isset($_SESSION['uid'])) {
  $groupid = $_POST['groupid'];
  $postid = $_POST['postid'];
  $uid = $_SESSION['uid'];

  //link handling
  if(empty($groupid) || empty($postid)) {
    exit();
  }
  else {
    $sql = "DELETE FROM groupposts WHERE id=? AND groupid=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"ss", $postid, $groupid);
      mysqli_stmt_execute($stmt);
      $sql = "UPDATE groups SET postcount=postcount-1 WHERE id=?";
      $stmt = mysqli_stmt_init($conn);
      if (!mysqli_stmt_prepare($stmt,$sql)) {
        exit();
      } else {
        mysqli_stmt_bind_param($stmt,"s", $groupid);
        mysqli_stmt_execute($stmt);
        $sql = "DELETE FROM groupcomments WHERE postid=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql)) {
          exit();
        } else {
          mysqli_stmt_bind_param($stmt,"s", $postid);
          mysqli_stmt_execute($stmt);
          $sql = "DELETE FROM groupPostlikes WHERE postid=?";
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
  }
} else {
  exit();
}
