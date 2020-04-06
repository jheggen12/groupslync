<?php
session_start();
require '../dbh.php';
if (empty($_SESSION['uid'])) {
  echo '<p>You must be logged in to view your groups</p>';
  exit();
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
     <nav>
       <ul>
         <?php

         if (isset($_SESSION['uid'])) {
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="myGroups.php" class="currentPage">My Groups</a></li>
           <li class="mainli"><a href="findGroups.php">Find Group</a></li>
           <li class="mainli"><a href="createGroup.php">New Group</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>
            <li class="nav-uid"><a href="user.php?uid='.$_SESSION['uid'].'">'.$_SESSION['uid'].' <i class="fas fa-caret-down"></i></a>
              <ul>
                <li class="subli"><a href="help.php">Help</a></li>
                <li class="subli"><a href="includes/logout.php?action=logout">Logout</a></li>
            </ul></li>';
            $notifSql = 'SELECT*FROM notifications WHERE recipient=? ORDER BY id desc LIMIT 30';
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $notifSql)) {
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
     <header>
     </header>
     <main>
       <?php
       $uid = mysqli_real_escape_string($conn, $_SESSION['uid']);
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
