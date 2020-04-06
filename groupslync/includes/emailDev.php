<?php
session_start();
$message = $_POST['message'];
if(isset($_SESSION['uid'])) {
  $owner = $_SESSION['uid'];
  if(empty($message)) {
    echo 'Email failed to send. Try again.';
    exit();
  } else {
    $email = "joshwebdevel@gmail.com";
    $subject = $owner." feedback";
    $headers = "From: groupslync";
    mail($email, $subject, $message, $headers);
    echo 'Email Sent Successfully.<br>Thanks for your feedback.<br>';
    exit();
  }
}
