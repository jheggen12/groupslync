<?php
session_start();
require '../dbh.php';
require './commonFunctions.php';
if (empty($_SESSION['uid'])) {
  echo '<p>You must be logged in to view your groups</p>';
  exit();
} else {
  $uid = $_SESSION['uid'];
}
 ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>My Groups</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/myGroups.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  </head>
  <body class="mygroups" id="myGroups">
     <div id="scrollBlock"></div>
     <nav>
       <ul>
         <?php

         if (isset($uid)) {
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="myGroups.php" class="currentPage">My Groups</a></li>
           <li class="mainli"><a href="findGroups.php">Find Group</a></li>
           <li class="mainli"><a href="createGroup.php">New Group</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>
            <li class="nav-uid"><a href="user.php?uid='.$uid.'">'.$uid.' <i class="fas fa-caret-down"></i></a>
              <ul>
                <li class="subli"><a href="help.php">Help</a></li>
                <li class="subli"><a href="includes/logout.php?action=logout">Logout</a></li>
            </ul></li>';
            loadNotifications($uid, $conn);
         }
          ?>
       </ul>
     </nav>
     <header>
     </header>
     <main>
       <?php
       $sql = "SELECT grouplikes.unseenposts, groups.owner, groups.name, groups.id, groups.postcount, groups.likecount, groups.genre FROM grouplikes LEFT JOIN groups ON grouplikes.groupid = groups.id WHERE grouplikes.uid='$uid' ORDER BY grouplikes.unseenposts DESC, groups.likecount DESC";
       $result = mysqli_query($conn, $sql);
       if (mysqli_num_rows($result) > 0) {
         echo '<div id="groupList">';
         while($group = mysqli_fetch_assoc($result)) {

           echo '<div class="groupRow" data-groupid="'.$group['id'].'">';
           if($group['unseenposts'] > 0) {
             echo '<span class="unread">'.$group['unseenposts'].'</span>';
           }
           echo '<span class="name">'.$group['name'].'</span><div class="info"><span class="owner">Owner: '.$group['owner'].'</span>';
           if ($group['genre']) {
             echo '<span>Genre: '.$group['genre'].'</span>';
           }
           echo '</div><div class="totals"><span class="likes">Likes: '.$group['likecount'].'</span><span>Posts: '.$group['postcount'].'</span></div>';
           if($group['owner'] == $uid) { //trash can to delete group
             echo '<i class="fas fa-trash-alt" data-groupid="'.$group['id'].'"></i>';
           } else { //red X to unlike group
             echo '<i class="fas fa-times" data-groupid="'.$group['id'].'"></i>';
           }
           echo '</div>';
         }
         echo '</div>';
       } else {
        echo '<p>You don\'t have any groups yet.</p>
        <p>Search for groups <a href="findGroups.php">here</a>.</p>';
      }?>
    </main>
  </body>
  <script src="js/myGroups.js" type="text/javascript"></script>
  <script src="js/notifScript.js" type="text/javascript"></script>
</html>
