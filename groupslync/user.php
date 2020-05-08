<?php
  require '../dbh.php';
  require './commonFunctions.php';
  session_start();
  if(isset($_GET['group']) && $_GET['group'] == 'yes') {
    $group=1;
  } else {
    $group=0;
  }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>User</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/user.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/jquery.cookie.js"></script>
  </head>
  <body class="body1">
    <div id="scrollBlock"></div>
     <nav>
       <ul>
         <?php

         if (isset($_SESSION['uid'])) {
           $uid=$_SESSION['uid'];
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="myGroups.php">My Groups</a></li>
           <li class="mainli"><a href="findGroups.php">Find Group</a></li>
           <li class="mainli"><a href="createGroup.php">New Group</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>
           <li class="nav-uid"><a href="user.php?uid='.$uid.'">'.$uid.' <i class="fas fa-caret-down"></i></a>
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
?>    
   </ul>
     </nav>
     <main id="main">
<?php
$user = mysqli_real_escape_string($conn, $_GET['uid']);
$sql = "SELECT uid FROM users WHERE uid='$user'";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
  echo '<header>
    <span>'.$user.'</span></header>';
  if(isset($_GET['group'])) {
    if($_GET['group'] == 'yes') {
      echo '<div id="button"><p>Group</p><a href="user.php?uid='.$user.'">View public</a></div>';
    }
  } else {
    echo '<div id="button"><p>Public</p><a href="user.php?uid='.$user.'&group=yes">View group</a></div>';
  }

 echo '<div class="postAndComments" id="groupFeed">';
 if(!empty($group)) {
   $sql = "SELECT*FROM groupposts LEFT JOIN groups ON groupposts.groupid = groups.id WHERE groupposts.poster=? AND groups.private = 0 AND groupposts.type != 'text' ORDER BY groupposts.postdate DESC LIMIT 5";
   $stmt = mysqli_stmt_init($conn);
   if(!mysqli_stmt_prepare($stmt,$sql)) {
    echo "Page load failures.";
    exit();
   } else {
     mysqli_stmt_bind_param($stmt,"s",$user);
     mysqli_stmt_execute($stmt);
     $result = mysqli_stmt_get_result($stmt);
     $resultCheck = mysqli_num_rows($result);
     if ($resultCheck > 0) {
       echo '<div id="recentPosts"><h1>Recent Posts</h1><button class="playlistButton" data-uid="'.$user.'" data-type="posts" data-group="1">Create Playlist</button>';
       while ($post = mysqli_fetch_assoc($result)) {
          if ($post['type'] === 'spotLink') {
            echo '<iframe src="https://open.spotify.com/embed/track/'.$post['link'].'" height="80px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
          } elseif($post['type'] === 'spotPlaylist'){
            echo '<iframe src="https://open.spotify.com/embed/playlist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
          } elseif($post['type'] === 'spotAlbum'){
            echo '<iframe src="https://open.spotify.com/embed/album/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
          } elseif($post['type'] === 'spotArtist'){
            echo '<iframe src="https://open.spotify.com/embed/artist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
          }
       }
     } else {
       echo '<div id="recentPosts" style="display: block">
       <h1>Recent Posts</h1><p>Nothing to show here.</p>';
     }
   }
   echo '</div>';
    $sql = "SELECT groupposts.link, groupposts.type, groupposts.poster FROM groupPostlikes LEFT JOIN groupposts ON groupPostlikes.postid = groupposts.id WHERE groupPostlikes.uid=? AND groupposts.type != 'text' ORDER BY groupPostlikes.likedate DESC LIMIT 5";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)) {
     echo "Page load failures.";
     exit();
    } else {
      mysqli_stmt_bind_param($stmt,"s",$user);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $resultCheck = mysqli_num_rows($result);
      if ($resultCheck > 0) {
        echo '<div id="recentLikes">
        <h1>Recent Likes</h1><button class="playlistButton" data-uid="'.$user.'" data-type="likes" data-group="1">Create Playlist</button>';
        while ($post = mysqli_fetch_assoc($result)) {
          if ($post['type'] === 'spotLink') {
            echo '<iframe src="https://open.spotify.com/embed/track/'.$post['link'].'" height="80px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
          } elseif($post['type'] === 'spotPlaylist'){
            echo '<iframe src="https://open.spotify.com/embed/playlist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
          } elseif($post['type'] === 'spotAlbum'){
            echo '<iframe src="https://open.spotify.com/embed/album/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
          } elseif($post['type'] === 'spotArtist'){
            echo '<iframe src="https://open.spotify.com/embed/artist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
          }
         }
      } else {
        echo '<div id="recentLikes" style="display: block">
        <h1>Recent Likes</h1><p>Nothing to show here.</p>';
      }
    }
    echo '</div>';
} else {
  $sql = "SELECT*FROM publicposts WHERE poster=? AND type != 'text' ORDER BY postdate DESC LIMIT 5";
  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt,$sql)) {
   echo "Page load failures.";
   exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s",$user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
      echo '<div id="recentPosts">
      <h1>Recent Posts</h1><button class="playlistButton" data-uid="'.$user.'" data-type="posts" data-group="0">Create Playlist</button>';
      while ($post = mysqli_fetch_assoc($result)) {
        if ($post['type'] === 'spotLink') {
          echo '<iframe src="https://open.spotify.com/embed/track/'.$post['link'].'" height="80px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
        } elseif($post['type'] === 'spotPlaylist'){
          echo '<iframe src="https://open.spotify.com/embed/playlist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
        } elseif($post['type'] === 'spotAlbum'){
          echo '<iframe src="https://open.spotify.com/embed/album/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
        } elseif($post['type'] === 'spotArtist'){
          echo '<iframe src="https://open.spotify.com/embed/artist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
        }
      }
    } else {
      echo '<div id="recentPosts" style="display: block">
      <h1>Recent Posts</h1><p>Nothing to show here.</p>';
    }
  }
  echo '</div>';
   $sql = "SELECT publicposts.link, publicposts.type, publicposts.poster FROM publicPostlikes LEFT JOIN publicposts ON publicPostlikes.postid = publicposts.id WHERE publicPostlikes.uid=? AND type != 'text' ORDER BY publicPostlikes.likedate DESC LIMIT 5";
   $stmt = mysqli_stmt_init($conn);
   if(!mysqli_stmt_prepare($stmt,$sql)) {
    echo "Page load failures.";
    exit();
   } else {
     mysqli_stmt_bind_param($stmt,"s",$user);
     mysqli_stmt_execute($stmt);
     $result = mysqli_stmt_get_result($stmt);
     $resultCheck = mysqli_num_rows($result);
     if ($resultCheck > 0) {
       echo '<div id="recentLikes">
       <h1>Recent Likes</h1><button class="playlistButton" data-uid="'.$user.'" data-type="likes" data-group="0">Create Playlist</button>';
       while ($post = mysqli_fetch_assoc($result)) {
         if ($post['type'] === 'spotLink') {
           echo '<iframe src="https://open.spotify.com/embed/track/'.$post['link'].'" height="80px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
         } elseif($post['type'] === 'spotPlaylist'){
           echo '<iframe src="https://open.spotify.com/embed/playlist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
         } elseif($post['type'] === 'spotAlbum'){
           echo '<iframe src="https://open.spotify.com/embed/album/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
         } elseif($post['type'] === 'spotArtist'){
           echo '<iframe src="https://open.spotify.com/embed/artist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
         }
        }
     } else {
       echo '<div id="recentLikes" style="display: block">
       <h1>Recent Likes</h1><p>Nothing to show here.</p>';
     }
   }
   echo '</div>';
 }
} else {
 echo '<header style="width: 100%;">This user does not exist.</header>';
}

 ?>
        </header>
       </div>
    </main>
  </body>
  <script src="js/user.js" type="text/javascript"></script>
  <script src="js/commonFunctions.js" type="text/javascript"></script>
  <script src="js/notifScript.js" type="text/javascript"></script>
</html>
