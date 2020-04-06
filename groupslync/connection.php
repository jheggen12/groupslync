<?php
session_start();
require '../dbh.php';
if (isset($_GET['refresh_token'])) {
  setcookie('refresh_token', 'test', time() - (86400 * 30));
  setcookie("refresh_token", $_GET['refresh_token'], time() + (86400 * 365 *10), "/");
}
?>

<!doctype html>
<html lang="en">
  <head>
    <title>Spotify login</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/connection.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  </head>
  <nav>
       <ul>
         <?php

         if (isset($_SESSION['uid'])) {
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="myGroups.php">My Groups</a></li>
           <li class="mainli"><a href="findGroups.php">Find Group</a></li>
           <li class="mainli"><a href="createGroup.php">New Group</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>
           <li class="nav-uid"><a href="user.php?uid='.$_SESSION['uid'].'">'.$_SESSION['uid'].' <i class="fas fa-caret-down"></i></a>
              <ul>
                <li class="subli"><a href="help.php">Help</a></li>
                <li class="subli"><a href="includes/logout.php?action=logout">Logout</a></li>
            </ul></li>';
            $notifSql = 'SELECT*FROM notifications WHERE recipient=? ORDER BY id DESC LIMIT 30';
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
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="findGroups.php">Find Group</a></li>
           <li class="mainli"><a href="AccountSignUp.php">Sign Up</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>';
         }
      ?>      
   </ul>
     </nav>
  <body>
    <div class="container">
      <?php
      if (!empty($_GET['access_token'])){
        echo '<div id="loggedin">
          <h1>Login Successful.</h1>
          <br><br>
          <ul id="features">
            <li>Use the heart to save media to your Spotify library</li>
            <li>Create a playlist from the home feeds, groups, or user pages</li>
          </ul>
          <br>';
          if(!empty($_SESSION['uid'])) {
            echo '<a href="myGroups.php"><button id="button">Back to your groups</button></a>';
          } else {
            echo '<a href="index.php"><button id="button">Back to home page</button></a>';
          }
        echo '</div>';
      } else {
        echo '<div id="login">
          <h1>Log in with Spotify to save items to your library and create playlists.</h1>
          <br><br><br>';
          if(empty($_COOKIE['cookies'])) {
            echo '<div id="cookies">This site uses cookies to improve your experience.<br>This is required to connect your Spotify account with groupslync.<br>Click<span id="cookieLink">here</span>to accept</div>';
          } else {
            echo '<a href="https://musicauthbackend.herokuapp.com/login" class="btn btn-primary"><button id="button">Log in</button></a>';
          }
        echo '</div>';
      }
       ?>
    </div>
  </body>
  <script src="js/notifScript.js" type="text/javascript"></script>
  <script>
    $(document).ready(function() {
      let cookieLink = document.getElementById("cookieLink");
      if (cookieLink != null) {
        cookieLink.addEventListener("click", function() {
        let d = new Date();
        d.setTime(d.getTime() + 315360000000);
        let expires = "expires=" + d.toUTCString();
        timezone_offset_seconds = new Date().getTimezoneOffset() * 60;
        timezone_offset_seconds =
          timezone_offset_seconds == 0 ? 0 : -timezone_offset_seconds;
        document.cookie =
          "tzo=" + (timezone_offset_seconds + 21600) + ";expires=" + expires;
        document.cookie = "cookies=yes;expires=" + expires;
        window.location.reload();
        });
      }
    });
  </script>
</html>
