<?php
session_start();
require '../../dbh.php';

$commenttext = $_POST['text'];
$postid = $_POST['postid'];
$poster = $_POST['poster'];
$title = $_POST['title'];
$uid = $_SESSION['uid'];
//link handling
if(empty($commenttext) || empty($postid) || empty($poster)) {
  echo 'Empty fields';
  exit();
} else {
  $sql = "INSERT INTO groupcomments (commenttext, postid, commenter) VALUES (?, ?, ?)";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    echo 'Comment failed to post. Try again.';
    exit();
  } else { //Add a comment and reload the group page
    mysqli_stmt_bind_param($stmt,"sss", $commenttext, $postid, $uid);
    mysqli_stmt_execute($stmt);
    $newID = $conn->insert_id;
    echo '<div class="temp" id="comment'.$newID.'"><p>'.$commenttext.'<br><span>Posted by: <a href="user.php?uid='.$uid.'">'.$uid.'</a> just now </span></p>';
    echo '<h4 class="deleteComment temp" data-commentid="'.$newID.'">Delete Comment</h4>';
    echo '</div>';
    if ($uid != $poster) { //Don't send notification to yourself
      $content = "type=group&id=".$postid;
      $sql = "INSERT INTO notifications (recipient, type, comment, content, title, user) VALUES (?, 'comment', ?, ?, ?, ?)";
      $stmt = mysqli_stmt_init($conn);
      if(mysqli_stmt_prepare($stmt,$sql)) {
        mysqli_stmt_bind_param($stmt,"sssss", $poster, $commenttext, $content, $title, $uid);
        mysqli_stmt_execute($stmt);
      }
    }
  }
}
mysqli_stmt_close($stmt);
