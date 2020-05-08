<?php
  require '../dbh.php';
  require './commonFunctions.php';
  session_start();
 ?>

<!DOCTYPE html>

<html lang="en">
  <head>
    <title>Create Group</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/createGroup.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  </head>
  <body>
     <nav>
       <ul>
         <?php

         if (isset($_SESSION['uid'])) {
           $uid=$_SESSION['uid'];
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="myGroups.php">My Groups</a></li>
           <li class="mainli"><a href="findGroups.php">Find Group</a></li>
           <li class="mainli"><a href="createGroup.php" class="currentPage">New Group</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>
           <li class="nav-uid"><a href="user.php?uid='.$uid.'">'.$uid.' <i class="fas fa-caret-down"></i></a>
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
     <main>
     <h1>New Group</h1>
       <?php
       if(isset($_GET['error'])){
         $error = $_GET['error'];
         if($error == "emptyFields") {
           echo "Not all fields filled in properly. Please try again";
          } else if($error == 'groupnameInUse') {
            echo "That group name is already in use. Please try again";
          } else if ($error == 'invalidSql') {
            echo "An error occurred. Please try again.";
          } else if ($error == 'invalidEmail') {
            echo "You provided an invalid E-mail.";
          }
        }
       ?>
       <form class="form" action="includes/newGroup.php" method="POST">
         <input name="groupName" type="text" cols="50" maxlength="20" placeholder="                   Group Name"<?php
         if (!empty($_GET['groupName'])) {
           echo ' value="'.$_GET['groupName'].'" ';
         }
          ?>title="20 characters or less" required>
          <select name="genre">
            <option value="" default>Genre</option>
            <option value="Variety">Variety</option>
            <option value="Hip-hop">Hip-hop/Rap</option>
            <option value="R&B">R&B</option>
            <option value="Indie">Indie/Alternative</option>
            <option value="Pop">Pop</option>
            <option value="Rock">Rock</option>
            <option value="Jazz">Jazz</option>
            <option value="Metal">Metal</option>
            <option value="Country">Country</option>
            <option value="Electronic">Electronic</option>
          </select>
          <br>
          <label for="privacy"> Private  </label>
         <input class="checkbox" type="checkbox" name="privacy" value="1">
         <h1>Send Invites</h1>
         <textarea name="invitees" type="text" rows="4" cols="30" maxlength="1000" placeholder="&#10;         Usernames, Emails..."></textarea>
         <textarea name="message" type="text" rows="4" cols="30" maxlength="1000" placeholder="&#10;             Message to send.."></textarea>
         <button id="button" type="submit" name="Create-Group-Submit">Create Group</button>
       </form>
        </main>
  </body>
  <script src="js/notifScript.js" type="text/javascript"></script>
</html>
