<?php
session_start();
require '../../dbh.php';

if(empty($_POST['delete'])) {
  die();
}

if(!empty($_SESSION['uid'])) {
  $uid = $_SESSION['uid'];
  //link handling
  $sql = "DELETE FROM groupcomments WHERE commenter=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "DELETE FROM publiccomments WHERE commenter=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "DELETE FROM groupPostlikes WHERE uid=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "DELETE FROM publicPostlikes WHERE uid=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "DELETE FROM grouplikes WHERE uid=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "UPDATE groups SET owner='deleted' WHERE owner=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "UPDATE groupposts SET poster='deleted' WHERE poster=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "UPDATE publicposts SET poster='deleted' WHERE poster=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "DELETE FROM users WHERE uid=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
    mysqli_stmt_execute($stmt);
  }
  unset($_SESSION['uid']);
  session_destroy();
  echo 'Account Deleted';
  exit();
  mysqli_stmt_close($stmt);
} else {
  echo 'Page load failure';
}
