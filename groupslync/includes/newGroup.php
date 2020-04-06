<?php
session_start();
require '../../dbh.php';

if (isset($_POST['Create-Group-Submit'])){

  $groupname = $_POST['groupName'];
  $privacy = $_POST['privacy'];
  $owner = $_SESSION['uid'];
  $genre = $_POST['genre'];
  $invitees = $_POST['invitees'];
  $message = $_POST['message'];

  $invs = explode(",",$invitees);

  if(!isset($privacy)) {
    $privacy=0;
  }

  if(empty($groupname) || empty($owner)) {
    header("Location: ../createGroup.php?error=emptyFields&groupName=".$groupname);
    exit();
  } else {
    $sql = "SELECT name FROM groups WHERE name=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      header("Location: ../createGroup.php?error=invalidSql");
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s",$groupname);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_store_result($stmt);
      $resultCheck = mysqli_stmt_num_rows($stmt);
      if ($resultCheck > 0) {
        header("Location: ../createGroup.php?error=groupnameInUse");
        exit();
      } else {
        $sql = "INSERT INTO groups (name, owner, private, genre) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql)) {
          header("Location: ../createGroup.php?error=invalidSql&groupName=".$groupname);
          exit();
        } else { //Add a group to the database, like the group, and open the group
          mysqli_stmt_bind_param($stmt,"ssss",$groupname, $owner, $privacy, $genre);
          mysqli_stmt_execute($stmt);
          $newID = $conn->insert_id;
          $sql = "INSERT INTO grouplikes (uid, groupid) VALUES (?, ?)";
          $stmt = mysqli_stmt_init($conn);
          if (!mysqli_stmt_prepare($stmt,$sql)) {
            header("Location: ../group.php?error=likeFailed&id=".$newID);
            exit();
          } else {
            mysqli_stmt_bind_param($stmt,"ss", $owner, $newID);
            mysqli_stmt_execute($stmt);
            $subject = $owner." wants you to join groupslync";
            $headers = "From: groupslync";
            $headers .= "\n".'Content-type: text/html; charset=utf-8';
            $txt = "User '".$owner."' has invited you to join the group '".$groupname."' on groupslync.".' Follow this <a href="https://www.groupslync.com/AccountSignUp.php">link</a> to create an account.';
            if (!empty($message)){
              $txt .= "<br><br>They sent you the following message:<br>".$message;
            }
            foreach($invs as $inv) {
              $inv = trim($inv);
              $sql = "SELECT uid FROM users WHERE uid='$inv'";
              $result = mysqli_query($conn, $sql);
              $exists = mysqli_num_rows($result);
              if($exists > 0) { //uid exists, add the user to the group,increment count & send notification???
                $sql = "SELECT*FROM grouplikes WHERE uid='$inv' AND groupid='$newID'";
                $result1 = mysqli_query($conn, $sql);
                $exists = mysqli_num_rows($result1);
                if ($exists > 0) {
                  continue;
                }
                $user = mysqli_fetch_assoc($result);
                $sql = "INSERT INTO grouplikes (uid, groupid) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt,$sql)) {
                  exit();
                } else {
                  mysqli_stmt_bind_param($stmt,"ss", $user['uid'], $newID);
                  mysqli_stmt_execute($stmt);
                  $sql = "UPDATE groups SET likecount=likecount+1 WHERE id=?";
                  $stmt = mysqli_stmt_init($conn);
                  if (!mysqli_stmt_prepare($stmt,$sql)) {
                    exit();
                  } else {
                    mysqli_stmt_bind_param($stmt,"s", $newID);
                    mysqli_stmt_execute($stmt);
                    $sql = "INSERT INTO notifications (recipient, type, content, title, user) VALUES (?, 'add', ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt,$sql)) {
                    } else {
                      mysqli_stmt_bind_param($stmt,"ssss", $user['uid'], $newID, $groupname, $owner);
                      mysqli_stmt_execute($stmt);
                    }
                  }
                }
                $sent = 1;
                continue;
              }
              $sql = "SELECT uid FROM users WHERE email='$inv'";
              $result = mysqli_query($conn, $sql);
              $exists = mysqli_num_rows($result);
              if($exists > 0) { //email exists, add the user to the group,increment count & send notification
                $user = mysqli_fetch_assoc($result);
                $sql = "SELECT*FROM grouplikes WHERE uid=".$user['uid']." AND groupid='$newID'";
                $result1 = mysqli_query($conn, $sql);
                $exists = mysqli_num_rows($result1);
                if ($exists > 0) {
                  continue;
                }
                $sql = "INSERT INTO grouplikes (uid, groupid) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt,$sql)) {
                  continue;
                } else {
                  mysqli_stmt_bind_param($stmt,"ss", $user['uid'], $newID);
                  mysqli_stmt_execute($stmt);
                  $sql = "UPDATE groups SET likecount=likecount+1 WHERE id=?";
                  $stmt = mysqli_stmt_init($conn);
                  if (!mysqli_stmt_prepare($stmt,$sql)) {
                    continue;
                  } else {
                    mysqli_stmt_bind_param($stmt,"s", $newID);
                    mysqli_stmt_execute($stmt);
                    $sql = "INSERT INTO notifications (recipient, type, content, title, user) VALUES (?, 'add', ?, ?, ?)";
                    $stmt = mysqli_stmt_init($conn);
                    if (!mysqli_stmt_prepare($stmt,$sql)) {
                    } else {
                      mysqli_stmt_bind_param($stmt,"ssss", $user['uid'], $newID, $groupname, $owner);
                      mysqli_stmt_execute($stmt);
                    }
                  }
                }
                $sent = 1;
                continue;
              } else { //email not in use, send them an e-mail
                if (!filter_var($inv, FILTER_VALIDATE_EMAIL)) {
                  continue;
                }
                mail($inv, $subject, $txt, $headers);
                $sql = "INSERT INTO outstandinginvites (email, groupid) VALUES (?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt,$sql)) {
                  continue;
                } else {
                  mysqli_stmt_bind_param($stmt,"ss", $inv, $newID);
                  mysqli_stmt_execute($stmt);
                }
              }
            }
            header("Location: ../group.php?id=".$newID);
            exit();
          }
        }
      }
    }
  }
  mysqli_stmt_close($stmt);
} else {
  header("Location: ../createGroup.php");
  exit();
}
