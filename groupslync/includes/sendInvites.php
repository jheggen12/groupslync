<?php
session_start();
require '../../dbh.php';

$groupid = $_POST['groupid'];
$groupname = $_POST['groupname'];
$owner = $_SESSION['uid'];
$invitees = $_POST['emails'];
$message = $_POST['message'];

$invs = explode(",",$invitees);

if(empty($invitees) || empty($groupname) || empty($owner)) {
 echo 'ERROR - <p id="errorMessage">Empty Fields</p>';
 exit();
}
$subject = $owner." wants you to join groupslync";
$headers = "From: groupslync";
$headers .= "\n".'Content-type: text/html; charset=utf-8';
$txt = "User '".$owner."' has invited you to join the group '".$groupname."' on groupslync.".' Follow this <a href="https://www.groupslync.com/AccountSignUp.php">link</a> to create an account.';
if (!empty($message)){
  $txt .= "<br><br>They sent you the following message:<br>".$message;
}
$sent = '';
foreach($invs as $inv) {
  $inv = trim($inv);
  $sql = "SELECT uid FROM users WHERE uid='$inv'";
  $result = mysqli_query($conn, $sql);
  $exists = mysqli_num_rows($result);
  if($exists > 0) { //uid exists, add the user to the group,increment count & send notification???
    $sql = "SELECT*FROM grouplikes WHERE uid='$inv' AND groupid='$groupid'";
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
      mysqli_stmt_bind_param($stmt,"ss", $user['uid'], $groupid);
      mysqli_stmt_execute($stmt);
      $sql = "UPDATE groups SET likecount=likecount+1 WHERE id=?";
      $stmt = mysqli_stmt_init($conn);
      if (!mysqli_stmt_prepare($stmt,$sql)) {
        exit();
      } else {
        mysqli_stmt_bind_param($stmt,"s", $groupid);
        mysqli_stmt_execute($stmt);
        $sql = "INSERT INTO notifications (recipient, type, content, title, user) VALUES (?, 'add', ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql)) {
        } else {
          mysqli_stmt_bind_param($stmt,"ssss", $user['uid'], $groupid, $groupname, $owner);
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
    $sql = "SELECT*FROM grouplikes WHERE uid=".$user['uid']." AND groupid='$groupid'";
    $result1 = mysqli_query($conn, $sql);
    $exists = mysqli_num_rows($result1);
    if ($exists > 0) {
      continue;
    }
    $sql = "INSERT INTO grouplikes (uid, groupid) VALUES (?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"ss", $user['uid'], $groupid);
      mysqli_stmt_execute($stmt);
      $sql = "UPDATE groups SET likecount=likecount+1 WHERE id=?";
      $stmt = mysqli_stmt_init($conn);
      if (!mysqli_stmt_prepare($stmt,$sql)) {
        exit();
      } else {
        mysqli_stmt_bind_param($stmt,"s", $groupid);
        mysqli_stmt_execute($stmt);
        $sql = "INSERT INTO notifications (recipient, type, content, title, user) VALUES (?, 'add', ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt,$sql)) {
        } else {
          mysqli_stmt_bind_param($stmt,"ssss", $user['uid'], $groupid, $groupname, $owner);
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
      exit();
    } else {
      mysqli_stmt_bind_param($stmt,"ss", $inv, $groupid);
      mysqli_stmt_execute($stmt);
      $sent = 1;
    }

  }
}
if (!empty($sent)){
  echo 'Invites sent.';
} else {
  echo 'ERROR - <p id="errorMessage">No valid invites.</p>';
}