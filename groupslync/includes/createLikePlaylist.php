<?php
session_start();
require '../../dbh.php';

$uid = $_POST['uid'];
$type = $_POST['type'];
$group = $_POST['group'];

if (!isset($uid) || !isset($type) || !isset($group)) {
  exit();
}
if($type == 'likes') {
  if ($group == 1) {
    $sql = "SELECT*FROM groupPostlikes LEFT JOIN groupposts ON groupPostlikes.postid = groupposts.id WHERE groupposts.poster=? AND groupposts.private = 0 AND groupposts.type = 'spotLink' ORDER BY groupPostlikes.likedate DESC LIMIT 100";
  } elseif ($group == 0) {
    $sql = "SELECT*FROM publicPostlikes LEFT JOIN publicposts ON publicPostlikes.postid = publicposts.id WHERE publicposts.poster=? AND publicposts.type = 'spotLink' ORDER BY publicPostlikes.likedate DESC LIMIT 100";
  }
} elseif ($type == 'posts') {
  if ($group == 1) {
    $sql = "SELECT*FROM groupposts WHERE poster=? AND private = 0 AND type = 'spotLink' ORDER BY postdate DESC LIMIT 100";
  } elseif ($group == 0) {
    $sql = "SELECT*FROM publicposts WHERE poster=? AND type = 'spotLink' ORDER BY postdate DESC LIMIT 100";
  }
} else {
  exit();
}

$stmt = mysqli_stmt_init($conn);
if(!mysqli_stmt_prepare($stmt,$sql)) {
  echo "Error";
  exit();
} else {
  mysqli_stmt_bind_param($stmt,"s",$uid);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $resultCheck = mysqli_num_rows($result);
  if ($resultCheck > 0) {
    $first = true;
    $playlist = array();
    while($song = mysqli_fetch_assoc($result)) {
      if ($first) {
        echo 'spotify:track:'.$song['link'];
        $first = false;
      } else {
        echo ',spotify:track:'.$song['link'];
      }
    }
  } else {
    echo 'Error1';
  }
  mysqli_stmt_close($stmt);
}
