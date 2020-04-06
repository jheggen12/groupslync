<?php
session_start();
require '../../dbh.php';

$commentNewCount = $_POST['numComments'];
if (isset($_POST['postId'])) {
  if(!empty($_COOKIE['tzo'])) {
    $tz_offset = $_COOKIE['tzo'];
  } else {
    $tz_offset = -21600;
  }
  $postid = $_POST['postId'];
  $sql = "SELECT*FROM groupcomments WHERE postid=? ORDER BY commentdate LIMIT 6 OFFSET ".$commentNewCount;
  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt,$sql)) {
    echo "Page load failure.";
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s",$postid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 5) {
      for ($i=0; $i<5; $i++) {
        $comment = mysqli_fetch_assoc($result);
        echo '<div id="comment'.$comment['id'].'"><p>'.$comment['commenttext'].'<br><span>Posted by: <a href="user.php?uid='.$comment['commenter'].'">'.$comment['commenter'].'</a> on '.date("M j, g:i a",strtotime($comment['commentdate']) + $tz_offset).'</span></p>';
        if (isset($_SESSION['uid']) && $_SESSION['uid'] == $comment['commenter']) { //Red X to delete comment if it is the users comment
          echo '<h4 class="deleteComment" data-commentid="'.$comment['id'].'">Delete Comment</h4>';
        }
        echo '</div>';
      }
      echo '<button id="loadMoreCommButton'.$postid.'" class="loadMoreCommButton" data-comments="'.($commentNewCount + 5).'" data-postid="'.$postid.'">Load more comments</button>';
    } else {
      while ($comment = mysqli_fetch_assoc($result)) {
        echo '<div id="comment'.$comment['id'].'"><p>'.$comment['commenttext'].'<br><span>Posted by: <a href="user.php?uid='.$comment['commenter'].'">'.$comment['commenter'].'</a> on '.date("M j, g:i a",strtotime($comment['commentdate']) + $tz_offset).'</span></p>';
        if (isset($_SESSION['uid']) && $_SESSION['uid'] == $comment['commenter']) { //Red X to delete comment if it is the users comment
          echo '<h4 class="deleteComment" data-commentid="'.$comment['id'].'">Delete Comment</h4>';
        }
        echo '</div>';
      }
    }
  }
} else {
echo '<p>Comments load failure.</p>';
}
   ?>
