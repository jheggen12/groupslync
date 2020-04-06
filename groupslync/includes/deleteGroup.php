<?php
session_start();
require '../../dbh.php';

$groupid = $_POST['groupid'];

if(isset($_SESSION['uid'])) {
  $sql = "SELECT id FROM groupposts WHERE groupid=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt, $sql)) {
    header("Location: ../index.php");
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $groupid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($post = mysqli_fetch_assoc($result)) { //For each post, delete likes and comments
      $postid = $post['id'];
      $sql = "DELETE FROM groupcomments WHERE postid=?";
      $stmt = mysqli_stmt_init($conn);
      if (!mysqli_stmt_prepare($stmt,$sql)) {
        header("Location: ../index.php");
        exit();
      } else {
        mysqli_stmt_bind_param($stmt,"s", $postid);
        mysqli_stmt_execute($stmt);
      }
      $sql = "DELETE FROM groupPostlikes WHERE postid=?";
      $stmt = mysqli_stmt_init($conn);
      if (!mysqli_stmt_prepare($stmt,$sql)) {
        header("Location: ../index.php");
        exit();
      } else {
        mysqli_stmt_bind_param($stmt,"s", $postid);
        mysqli_stmt_execute($stmt);
      }
    }
    $sql = "DELETE FROM grouplikes WHERE groupid=?";//Delete group likes
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
      header("Location: ../index.php");
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s", $groupid);
      mysqli_stmt_execute($stmt);
    }
    $sql = "DELETE FROM outstandinginvites WHERE groupid=?"; //Delete outstanding invites
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s", $groupid);
      mysqli_stmt_execute($stmt);
    }
    $sql = "DELETE FROM groupposts WHERE groupid=?"; //Delete outstanding invites
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s", $groupid);
      mysqli_stmt_execute($stmt);
    }
    $sql = "DELETE FROM groups WHERE id=?"; //Delete outstanding invites
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s", $groupid);
      mysqli_stmt_execute($stmt);
    }
  }
} else {
  header("Location: ../index.php");
  exit();
}
