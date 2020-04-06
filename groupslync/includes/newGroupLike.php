<?php
session_start();
require '../../dbh.php';

$groupid = $_POST['groupid'];
$uid = $_SESSION['uid'];
$host = $_POST['host'];
$title = $_POST['title'];

//link handling
if(empty($groupid) || empty($uid) || empty($host) || empty($title)){
  echo 'Like failed.';
  exit();
}
else {
  $sql = "INSERT INTO grouplikes (uid, groupid) VALUES (?, ?)";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    echo 'Like failed.';
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"ss", $uid, $groupid);
    mysqli_stmt_execute($stmt);
    $sql = "UPDATE groups SET likecount=likecount+1 WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      echo 'Group liked.';
    } else {
      mysqli_stmt_bind_param($stmt,"s", $groupid);
      mysqli_stmt_execute($stmt);
      echo 'Group liked.';
    }
    $sql = "INSERT INTO notifications (recipient, type, content, title, user) VALUES (?, 'join', ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      echo 'Group liked.';
    } else {
      mysqli_stmt_bind_param($stmt,"ssss", $host, $groupid, $title, $uid);
      mysqli_stmt_execute($stmt);
      echo 'Group liked.';
    }
  }
  mysqli_stmt_close($stmt);
}
