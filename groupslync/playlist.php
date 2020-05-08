<?php
  require '../dbh.php';
  require './commonFunctions.php';
  session_start();
  if(!isset($_GET['id'])) {
    die();
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Playlist</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/playlist.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/jquery.cookie.js"></script>
  </head>
  <body class="body1">
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
            loadNotifications($uid, $conn);
         } else {
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="findGroups.php">Find Group</a></li>
           <li class="mainli"><a href="AccountSignUp.php">Sign Up</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>';
         }
?>       </ul>
     </nav>
     <main>
<?php
 echo '<div class="postAndComments" id="groupFeed"><div id="recentPosts">';
 if (!empty($_GET['id'])) {
   $playlistId = $_GET['id'];
 }
 else {
   echo 'This playlist does not exist.';
   die();
 }

 echo '<iframe src="https://open.spotify.com/embed/playlist/'.$playlistId.'" height="410px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
echo '</div><button id="deleteButton" data-playlistid="'.$playlistId.'">Delete playlist</button></div>';
 ?>
    </main>
  </body>
  <script src="js/playlist.js" type="text/javascript"></script>
  <script src="js/notifScript.js" type="text/javascript"></script>
</html>