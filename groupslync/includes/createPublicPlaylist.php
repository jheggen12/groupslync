<?php
session_start();
require '../../dbh.php';

$genre = $_POST['genre'];
$sort = $_POST['sort'];
if(!empty($genre)) {
  if (!empty($sort) && $sort == 'new') {
    $sql = 'SELECT link FROM publicposts WHERE genre=? AND type="spotLink" ORDER BY postdate DESC LIMIT 100';
  } else {
    $sql = 'SELECT link FROM publicposts WHERE genre=? AND postdate > DATE_SUB(CURDATE(), INTERVAL 28 DAY) AND type="spotLink" ORDER BY likes DESC LIMIT 100';
  }
} else {
  if (!empty($sort) && $sort == 'new') {
    $sql = 'SELECT link FROM publicposts WHERE type="spotLink" ORDER BY postdate DESC LIMIT 100';
  } else {
    $sql = 'SELECT link FROM publicposts WHERE postdate > DATE_SUB(CURDATE(), INTERVAL 28 DAY) AND type="spotLink" ORDER BY likes DESC LIMIT 100';
  }
}
$stmt = mysqli_stmt_init($conn);
if(!mysqli_stmt_prepare($stmt,$sql)) {
  echo "Error1";
  exit();
} else {
  if(!empty($genre)) {
    mysqli_stmt_bind_param($stmt,"s",$genre);
  }
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $resultCheck = mysqli_num_rows($result);
  if ($resultCheck > 0) {
    $first = true;
    while($song = mysqli_fetch_assoc($result)) {
      if ($first) {
        echo 'spotify:track:'.$song['link'];
        $first = false;
      } else {
        echo ',spotify:track:'.$song['link'];
      }
    }
  } else {
    echo 'Error2';
  }
  mysqli_stmt_close($stmt);
}
