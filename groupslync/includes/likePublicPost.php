<?php
session_start();

require '../../dbh.php';

$postid = $_POST['postid'];
$title = $_POST['title'];
$poster = $_POST['poster'];
$uid = $_SESSION['uid'];

//link handling
if(empty($postid) || empty($uid) ){
  exit();
}
else {
  $sql = "INSERT INTO publicPostlikes (uid, postid) VALUES (?, ?)";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"ss", $uid, $postid);
    mysqli_stmt_execute($stmt);
  }
  $sql = "UPDATE publicposts SET likes = likes + 1 WHERE id=?";
  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $postid);
    mysqli_stmt_execute($stmt);
  }
  if ($uid != $poster) { //Don't send notification to yourself
    $content = "type=public&id=".$postid;
    $sql = "INSERT INTO notifications (recipient, type, content, title, user) VALUES (?, 'like', ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)) {
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"ssss", $poster, $content, $title, $uid);
      mysqli_stmt_execute($stmt);
    }
  }
  mysqli_stmt_close($stmt);
}
