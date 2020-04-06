<?php
session_start();

if (isset($_GET['action'])){
  unset($_SESSION['uid']);
  session_destroy();
  header("Location: ../index.php?action=logout");
  exit();
} else {
  header("Location: ../index.php");
  exit();
}
