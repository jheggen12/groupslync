<?php
  require '../dbh.php';
  require './commonFunctions.php';
  session_start();
 ?>

<!DOCTYPE html>

<html lang="en">
  <head>
    <title>Help</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/help.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
  </head>
  <body class="body">
     <nav>
       <ul>
        <?php
        if (isset($_SESSION['uid'])) {
          $uid = $_SESSION['uid'];
          echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
          <li class="mainli"><a href="myGroups.php">My Groups</a></li>
          <li class="mainli"><a href="findGroups.php">Find Group</a></li>
          <li class="mainli"><a href="createGroup.php">New Group</a></li>
          <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>
          <li class="nav-uid"><a href="user.php?uid='.$uid.'">'.$uid.'<i class="fas fa-caret-down"></i></a>
            <ul>
              <li class="subli"><a href="help.php">Help</a></li>
              <li class="subli"><a href="includes/logout.php?action=logout">Logout</a></li>
            </ul></li>';
            loadNotifications($uid, $conn);
         } else {
           die();
         }
          ?>
       </ul>
     </nav>
     <?php
     if(isset($_GET['error'])){
       $error = $_GET['error'];
       if($_GET['error'] == "emptyFields") {
         echo "Not all fields filled in properly. Please try again";
        } else if($error == 'groupnameInUse') {
          echo "That group name is already in use. Please try again";
        } else if ($error == 'invalidSql') {
          echo "An error occurred. Please try again.";
        }
      }
     ?>
     <main>
      <h1>How can we help?</h1>
       <ul id="options">
         <li class="password">Password Change</li>
         <li class="email">Email the developer</li>
         <li class="delete">Delete Account</li>
       </ul>
       <div id="message"><br>
         <?php
         if (isset($_GET['action'])) {
           if ($_GET['action'] == 'changed') {
             echo 'Password Change Successful<br>';
           } else if ($_GET['action'] == 'deleted') {
             echo 'Account Deleted Successfully<br>';
           }
         }
         ?>
       </div>

        <?php
        if(isset($_GET['error'])) {
          echo '<div id="passwordSection">';
        } else {
          echo '<div id="passwordSection" style="display: none;">';
        }
        //error handling for user creation
        if (isset($_GET['error'])) {
          if ($_GET['error'] == 'emptyFields'){
            echo '<h5>Please fill out all of the fields</h5>';
          } elseif ($_GET['error'] == 'uidNotExist') {
            echo '<h5>Username does not exist. Please try again.</h5>';
          }  elseif ($_GET['error'] == 'wrongPassword') {
            echo '<h5>Old password is incorrect. Please try again.</h5>';
          } elseif ($_GET['error'] == 'invalidpassword') {
            echo '<h5>New password is invalid. Password must be 8-16 characters. Please try again.</h5>';
          } elseif ($_GET['error'] == 'invalidMatch') {
            echo '<h5>Passwords do not match. Please try again.</h5>';
          } elseif ($_GET['error'] == 'invalidSql') {
            echo '<h5>Something went wrong. Please try again.</h5>';
          }
        }

       ?><br>
        <form class="form" action="includes/passwordChange.php" method="post">
          <input name="uid" type="text"<?php
          if(isset($_GET['uid'])) {
            echo 'value='.$_GET['uid'];
          }
          ?> placeholder="Username" required>
          <br>
          <input name="oldpwd" type="password" placeholder="Old Password" autocomplete="off" required>
          <br>
          <input name="newpwd" type="password" placeholder="New Password" required>
          <br>
          <input name="newpwdrpt" type="password" placeholder="Verify New Password" title="8-16 characters" required>
          <br><br>
          <button id="passwordButton" class="submit" name="chg-pwd-submit" type="submit">Change Password</button>
        </form>
      </div>
      <div id="emailSection" style="display: none;">
        <textarea id="messageContent" placeholder="Write your message here" rows="10" cols="50"></textarea><br>
        <button id="emailDev">Send feedback</button>
      </div>
      <div id="deleteSection" style="display: none;">
        <p>This will delete your groups, posts, and comments.<br> This action cannot be undone.</p>
        <button id="deleteAccount">Delete your account</button>
      </div>
    </main>
  </body>
  <script src="js/help.js" type="text/javascript"></script>
  <script src="js/notifScript.js" type="text/javascript"></script>
</html
