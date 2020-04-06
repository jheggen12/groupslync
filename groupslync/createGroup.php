<?php
  require '../dbh.php';
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
  <body class="body2">
     <nav>
       <ul>
         <?php

         if (isset($_SESSION['uid'])) {
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="myGroups.php">My Groups</a></li>
           <li class="mainli"><a href="findGroups.php">Find Group</a></li>
           <li class="mainli"><a href="createGroup.php" class="currentPage">New Group</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>
           <li class="nav-uid"><a href="user.php?uid='.$_SESSION['uid'].'">'.$_SESSION['uid'].' <i class="fas fa-caret-down"></i></a>
              <ul>
                <li class="subli"><a href="help.php">Help</a></li>
                <li class="subli"><a href="includes/logout.php?action=logout">Logout</a></li>
            </ul></li>';
            $notifSql = 'SELECT*FROM notifications WHERE recipient=? ORDER BY id desc LIMIT 30';
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt,$notifSql)) {
              exit();
            } else {
              mysqli_stmt_bind_param($stmt,"s", $_SESSION['uid']);
              mysqli_stmt_execute($stmt);
              $notifResult = mysqli_stmt_get_result($stmt);
              $notifCheck = mysqli_num_rows($notifResult);
              if($notifCheck > 0){
                echo '<li style="float: right;" class="mainli"><i class="fas fa-bell"></i><ul id="notifications">';
                echo '<button id="clearNotif">Clear</button>';
                while ($notif = mysqli_fetch_assoc($notifResult)) { //in group, handle null title for text post
                  if ($notif['type'] == "like") {
                    echo '<li><a href="post.php?'.$notif['content'].'">'.$notif['user'].' liked your post "'.$notif['title'].'"</a></li>';
                  } elseif ($notif['type'] == "comment") {
                    echo '<li><a href="post.php?'.$notif['content'].'">'.$notif['user'].' commented "'.$notif['comment'].'" on your post "'.$notif['title'].'"</a></li>';
                  } elseif ($notif['type'] == "join") {
                    echo '<li><a href="group.php?id='.$notif['content'].'">'.$notif['user'].' joined your group "'.$notif['title'].'"</a></li>';
                  } elseif ($notif['type'] == "add") {
                    echo '<li><a href="group.php?id='.$notif['content'].'">'.$notif['user'].' added you to group "'.$notif['title'].'"</a></li>';
                  }
                }
                echo '</ul></li>';
              }
            }
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
         <textarea name="invitees" type="text" rows="4" cols="30" maxlength="1000" placeholder="&#10;         Usernames, Emails..."></textarea>
         <textarea name="message" type="text" rows="4" cols="30" maxlength="1000" placeholder="&#10;             Message to send.."></textarea>
         <button id="button" type="submit" name="Create-Group-Submit">Create Group</button>
       </form>
        </main>
  </body>
  <script src="js/notifScript.js" type="text/javascript"></script>
</html>
