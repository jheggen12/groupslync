<?php
require '../../dbh.php';

if (isset($_POST['chg-pwd-submit'])){

  $uid = $_POST['uid'];
  $oldpwd = $_POST['oldpwd'];
  $password = $_POST['newpwd'];
  $passwordrepeat = $_POST['newpwdrpt'];

  if(empty($uid) || empty($oldpwd) || empty($password) || empty($passwordrepeat)) {
    header("Location: ../help.php?error=emptyFields&uid=".$uid);
    exit();
  } elseif(!preg_match("/^[0-9A-Za-z!@#$%]{8,16}$/", $password)) {
    header("Location: ../help.php?error=invalidpassword&uid=".$uid);
    exit();
  } elseif($password !== $passwordrepeat) {
    header("Location: ../help.php?error=invalidMatch&uid=".$uid);
    exit();
  } else {
    $sql = "SELECT uid,password FROM users WHERE uid=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      header("Location: ../help.php?error=invalidSql");
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s",$uid);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $user = mysqli_fetch_assoc($result);
      $pwdCheck = password_verify($oldpwd, $user['password']);
      if($pwdCheck == false) {
        header("Location: ../help.php?error=wrongPassword&uid=".$uid);
        exit();
      } elseif($pwdCheck == true) {
        $sql = "UPDATE users SET password=? WHERE uid=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql)) {
          header("Location: ../help.php?error=invalidSql&uid=".$uid);
          exit();
        } else { //Add a user to the database
          $hashedPwd = password_hash($password,PASSWORD_DEFAULT);
          mysqli_stmt_bind_param($stmt,"ss", $hashedPwd, $uid);
          mysqli_stmt_execute($stmt);
          header("Location: ../help.php?action=changed");
          exit();
        }
      } else {
        header("Location: ../help.php?error=uidNotExist");
        exit();
      }
    }
    mysqli_stmt_close($stmt);
  }
} else {
  header("Location: ../help.php");
  exit();
}
