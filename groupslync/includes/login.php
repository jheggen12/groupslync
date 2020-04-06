<?php

require '../../dbh.php';

if (isset($_POST['login-submit'])){

  $mailuid = $_POST['mailuid'];
  $password = $_POST['pwd'];
  $sql = "SELECT * FROM users WHERE uid=? OR email=?";
  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt,$sql)) {
    header("Location: ../index.php?error=invalidSql");
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"ss",$mailuid, $mailuid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if($row = mysqli_fetch_assoc($result)) {
      $pwdCheck = password_verify($password, $row['password']);
      if($pwdCheck == false) {
        header("Location: ../index.php?error=wrongPassword&uid=".$mailuid);
        exit();
      } elseif($pwdCheck == true) {
        session_start();
        $_SESSION['uid'] = $row['uid'];
        $sql = "UPDATE users SET lastlogin = CURRENT_TIMESTAMP WHERE uid=? OR email=?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt,$sql)) {
          header("Location: ../index.php");
          exit();
        } else {
          mysqli_stmt_bind_param($stmt,"ss", $mailuid, $mailuid);
          mysqli_stmt_execute($stmt);
          header("Location: ../index.php");
          exit();
        }
      }
    } else {
      header("Location: ../index.php?error=noSuchUid");
      exit();
    }
  }
  mysqli_stmt_close($stmt);
} else {
  header("Location: ../index.php");
  exit();
}
