<?php
  require '../dbh.php';
  session_start();
?>
<!DOCTYPE HTML>
<html lang="en">
  <head>
    <title>Sign Up</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <link rel="stylesheet" href="css/signup.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
  </head>
  <body>
     <nav>
       <ul>
         <?php

         if (isset($_SESSION['uid'])) {
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li></ul><p style="color: white;">You already have an account.</p>';
           die();
         } else {
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li><a href="findGroups.php">Find Group</a></li>
           <li><a href="AccountSignUp.php" class="currentPage">Sign Up</a></li>';
         }
          ?>
       </ul>
     </nav>
     <main>
          <h1>Register</h1>
           <?php
           if (isset($_GET['error'])) {
             if ($_GET['error'] == 'emptyFields'){
               echo '<h5>Please fill out all of the fields</h5>';
             } elseif ($_GET['error'] == 'invalidEmailAndPassword') {
               echo '<h5>Invalid Email and password. Please try again.</h5>';
             } elseif ($_GET['error'] == 'uidUse') {
               echo '<h5>Username is taken. Please try again.</h5>';
             } elseif ($_GET['error'] == 'emailInUse') {
               echo '<h5>Email is already attached to an account. Please try again.</h5>';
             } elseif ($_GET['error'] == 'invalidpassword') {
               echo '<h5>Invalid password. Please try again.</h5>';
             } elseif ($_GET['error'] == 'invalidEmail') {
               echo '<h5>Invalid Email. Please try again.</h5>';
             } elseif ($_GET['error'] == 'invalidMatch') {
               echo '<h5>Passwords do not match. Please try again.</h5>';
             } elseif ($_GET['error'] == 'invalidSql') {
               echo '<h5>Something went wrong. Please try again.</h5>';
             }
           } elseif(isset($_GET['signup'])) {
             echo '<h5>Sign up successful. Login to your new account below.</h5>';
             echo '<form id="login" action="includes/login.php" method="POST">
             <input id="mailuid" name="mailuid" type="text"';
             if (isset($_GET['uid'])) {
               echo ' value="'.$_GET['uid'].'"';
             }
             echo ' required><br>
             <input id="pwd" name="pwd" type="password" placeholder="Password" autocomplete="off" required><br>
             <button id="new-login-submit" name="login-submit" type="submit">Log in</button></form>';
           }

           if(!isset($_GET['signup'])) {
           echo '<form class="form" action="includes/signup.php" method="post">
           <input id="username" name="uid" type="text" minlength="3" maxlength="16" title="3-16 letters/numbers" autocomplete="off" placeholder="Username"';
             if (isset($_GET['uid'])) {
               echo ' value="'.$_GET['uid'].'"';
             }
             echo ' required>
             <br><input id="email" name="email" type="text" autocomplete="off" placeholder="E-mail Address"';
             if (isset($_GET['email'])) {
               echo ' value="'.$_GET['email'].'"';
             }
             echo ' required>
             <br>
             <input id="password" name="pwd" type="password" placeholder="Password" autocomplete="off" required>
             <br>
             <input id="passwordrepeat" name="pwdrpt" type="password" placeholder="Verify Password" autocomplete="off" required>
             <br><br>
             <button class="submit" name="signup-submit" type="submit">Create Account</button>
           </form>';
          } ?>
    </main>
  </body>
</html>
