<?php
require '../../dbh.php';

if (isset($_POST['signup-submit'])){

  $uid = $_POST['uid'];
  $email = $_POST['email'];
  $password = $_POST['pwd'];
  $passwordrepeat = $_POST['pwdrpt'];

  if(empty($uid) || empty($email) || empty($password) || empty($passwordrepeat)) {
    header("Location: ../AccountSignUp.php?error=emptyFields&uid=".$uid."&email=".$email);
    exit();
  } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL) && !preg_match("/^[0-9A-Za-z!@#$%]{8,16}$/", $password)) {
    header("Location: ../AccountSignUp.php?error=invalidEmailAndPassword&uid=".$uid);
    exit();
  } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../AccountSignUp.php?error=invalidEmail&uid=".$uid);
    exit();
  } elseif(!preg_match("/^[0-9A-Za-z!@#$%]{8,16}$/", $password)) {
    header("Location: ../AccountSignUp.php?error=invalidpassword&uid=".$uid."&email=".$email);
    exit();
  } elseif($password !== $passwordrepeat) {
    header("Location: ../AccountSignUp.php?error=invalidMatch&uid=".$uid."&email=".$email);
    exit();
  } else {
    $sql = "SELECT uid FROM users WHERE uid=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      header("Location: ../AccountSignUp.php?error=invalidSql");
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s",$uid);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      $resultCheck = mysqli_stmt_num_rows($stmt);
      if ($resultCheck > 0) {
        header("Location: ../AccountSignUp.php?error=uidUse&email=".$email);
        exit();
      } else {
        $sql = "SELECT email FROM users WHERE email=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql)) {
          header("Location: ../AccountSignUp.php?error=invalidSql");
          exit();
        } else {
          mysqli_stmt_bind_param($stmt,"s",$email);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_store_result($stmt);
          $resultCheck = mysqli_stmt_num_rows($stmt);
          if ($resultCheck > 0) {
            header("Location: ../AccountSignUp.php?error=emailInUse&uid=".$uid);
            exit();
          } else {
            $sql = "INSERT INTO users (uid, password, email) VALUES (?, ?, ?)";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt,$sql)) {
              header("Location: ../AccountSignUp.php?error=invalidSql");
              exit();
            } else { //Add a user to the database
              $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
              mysqli_stmt_bind_param($stmt,"sss",$uid, $hashedPwd, $email);
              mysqli_stmt_execute($stmt);
              $sql = "SELECT groupid FROM outstandinginvites WHERE email='$email'";
              $result = mysqli_query($conn, $sql);
              while ($row = mysqli_fetch_assoc($result)) {
                $sql = "INSERT INTO grouplikes (uid, groupid) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                  header("Location: ../AccountSignUp.php?signup=success&uid=".$uid);
                  exit();
                } else {
                  mysqli_stmt_bind_param($stmt, "ss", $uid, $row['groupid']);
                  mysqli_stmt_execute($stmt);
                  $sql = "SELECT owner,name FROM groups WHERE id=".$row['groupid'];
                  $result2 = mysqli_query($conn, $sql);
                  $group = mysqli_fetch_assoc($result2);
                  $owner = $group['owner'];
                  $groupname = $group['name'];
                  $sql = "INSERT INTO notifications (recipient, type, content, title, user) VALUES (?, 'join', ?, ?, ?)";
                  $stmt = mysqli_stmt_init($conn);
                  if (mysqli_stmt_prepare($stmt,$sql)) {
                    mysqli_stmt_bind_param($stmt,"ssss", $owner, $row['groupid'], $groupname, $uid);
                    mysqli_stmt_execute($stmt);
                  }
                  $sql = "UPDATE groups SET likecount=likecount+1 WHERE id=?";
                  $stmt = mysqli_stmt_init($conn);
                  if (mysqli_stmt_prepare($stmt,$sql)) {
                    mysqli_stmt_bind_param($stmt, "s", $row['groupid']);
                    mysqli_stmt_execute($stmt);
                  }
                }
              }
              $sql = "DELETE FROM outstandinginvites WHERE email=?";
              $stmt = mysqli_stmt_init($conn);
              if (mysqli_stmt_prepare($stmt,$sql)) {
                mysqli_stmt_bind_param($stmt,"s", $email);
                mysqli_stmt_execute($stmt);
              }
              header("Location: ../AccountSignUp.php?signup=success&uid=".$uid);
              exit();
            }
          }
        }
      }
    }
    mysqli_stmt_close($stmt);
  }
} else {
      header("Location: ../AccountSignUp.php");
      exit();
}
