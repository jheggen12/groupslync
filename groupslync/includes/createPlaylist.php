<?php
session_start();
require '../../dbh.php';

$groupid = $_POST['groupid'];

$sql = 'SELECT link FROM groupposts WHERE groupid=? AND type="spotLink" ORDER BY postdate DESC LIMIT 100';
$stmt = mysqli_stmt_init($conn);
if(!mysqli_stmt_prepare($stmt,$sql)) {
  echo "Error";
  exit();
} else {
  mysqli_stmt_bind_param($stmt,"s",$groupid);
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
    echo 'Error';
  }
  mysqli_stmt_close($stmt);
}
