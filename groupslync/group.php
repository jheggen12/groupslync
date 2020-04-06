<?php
  session_start();
  require '../dbh.php';
  if(!isset($_GET['id'])) {
    die();
  }
  if(isset($_SESSION['uid'])) { //reset unread posts when you open the group
    $uid = $_SESSION['uid'];
    $groupid = $_GET['id'];
    $sql = "UPDATE grouplikes SET unseenposts = 0 WHERE groupid=? AND uid=?";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt,$sql)) {
    } else {
      mysqli_stmt_bind_param($stmt,"ss",$groupid, $uid);
      mysqli_stmt_execute($stmt);
    }
  }

 ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
    $groupid = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT owner,genre,name,private FROM groups WHERE id='$groupid'";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    $group = mysqli_fetch_assoc($result);
    $owner = $group['owner'];
    $groupname = $group['name'];
    $private = $group['private'];
     echo '<title>'.$groupname.'</title>';
    ?>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/group.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/jquery.cookie.js"></script>
  </head>
  <body class="body">
     <nav>
       <ul>
<?php
if (isset($_SESSION['uid'])) {
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
  $notifSql = 'SELECT*FROM notifications WHERE recipient=? ORDER BY id DESC LIMIT 30';
  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt,$notifSql)) {
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s", $uid);
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
     <main class="group">
       <?php
       echo '<div class="leftSidebar">';
       if ($resultCheck < 1) {
         echo 'This group does not exist.';
          die();
       }
        if(!empty($_COOKIE['refresh_token'])) {
          $refresh = 1;
        } else {
          $refresh = 0;
        }
        if ($private) {//check if user is a member for private groups
          if (!empty($_SESSION['uid'])) {
            $sql = "SELECT uid FROM grouplikes WHERE uid='$uid' AND groupid='$groupid'";
            $result = mysqli_query($conn, $sql);
            $resultCheck = mysqli_num_rows($result);
            if ($resultCheck == 0 && $uid != 'josh') {// Not a member
              echo 'You do not have access to this group!';
              die();
            }
          } else {
            echo 'You do not have access to this group!';
            die(); //access denied
          }
        }
        echo "<h2>".$groupname."</h2>";
        if (!empty($group['genre'])) {
          echo '<h3>'.$group['genre'].'</h3>';
        }
        if ( $private == 1) {
          echo "<h4>Private group</h4>";
        }
         if (!empty($uid) && !empty($groupid)) {
           $uid = mysqli_real_escape_string($conn, $uid);
           $sql = "SELECT groups.name, groups.id, groups.postcount, groups.likecount FROM grouplikes LEFT JOIN groups ON grouplikes.groupid = groups.id WHERE grouplikes.uid='$uid' ORDER BY groups.name";
           $result = mysqli_query($conn, $sql);
           if (mysqli_num_rows($result) > 1) {
             echo '<div id="otherGroups"><p>Groups </p><ul>';
             while($group = mysqli_fetch_assoc($result)) {
               if ($group['id'] != $groupid) {
                 echo '<li><a class="otherGroups" href="group.php?id='.$group['id'].'">'.$group['name'].'</a></li>';
               }
             }
             echo '</ul></div>';
           }
        }
        echo '<div id="leftButtons">';
        if(!empty($uid)) { //leave/join group button
          $sql = "SELECT*FROM grouplikes WHERE uid='$uid' AND groupid='$groupid'";
          $result = mysqli_query($conn, $sql);
          if ($owner == $uid) {
            echo '<button class="leftButton" id="leftButton" data-groupid="'.$groupid.'" data-action="delete">Delete Group</button>';
          } elseif (mysqli_num_rows($result) > 0){
            echo '<button class="leftButton" id="leftButton" data-groupid="'.$groupid.'" data-action="leave">Leave Group</button>';
          } else {
            echo '<button class="leftButton" id="leftButton" data-host="'.$owner.'" data-title="'.$groupname.'" data-groupid="'.$groupid.'" data-action="join">Join Group</button>';
          }
        }
         if ($refresh) {
           echo '<button class="leftButton" id="playlistButton" data-groupname="'.$groupname.'" data-groupid="'.$groupid.'">Create playlist from songs</button>';
         } else {
           echo '<a href="connection.php"><button class="leftButton">Login with Spotify to create a playlist</button></a>';
         }
        echo '</div></div>'; //Close off left sidebar divs
          ?>
      <div class="postAndComments" id="groupFeed">
<?php
if (isset($groupid)) {
  if(!empty($_COOKIE['tzo'])) {
    $tz_offset = $_COOKIE['tzo'];
  } else {
    $tz_offset = 3600; //-21600
  }
  $sql = 'SELECT*FROM groupposts WHERE groupid=? ORDER BY postdate DESC LIMIT 8';
  $stmt = mysqli_stmt_init($conn);
  if(!mysqli_stmt_prepare($stmt,$sql)) {
    echo "Page load failures.";
    exit();
  } else {
    mysqli_stmt_bind_param($stmt,"s",$groupid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
      while ($post = mysqli_fetch_assoc($result)) {
        echo '<div id="post'.$post['id'].'" data-groupid="'.$groupid.'" data-postid="'.$post['id'].'" data-poster="'.$post['poster'].'" data-title="'.$post['title'].'" class="main"><div class="post">';
        if (isset($_SESSION['uid']) && $_SESSION['uid'] == $post['poster']) { //Red X to delete post if it is the users post
          echo '<h4 class="deletePost">Delete Post</h4>';
        }
        if ($post['type'] === 'spotLink') {
          echo '<iframe src="https://open.spotify.com/embed/track/'.$post['link'].'" height="80px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
        } elseif($post['type'] === 'spotPlaylist'){
          echo '<iframe src="https://open.spotify.com/embed/playlist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
        } elseif($post['type'] === 'spotAlbum'){
          echo '<iframe src="https://open.spotify.com/embed/album/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
        } elseif($post['type'] === 'spotArtist'){
          echo '<iframe src="https://open.spotify.com/embed/artist/'.$post['link'].'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
        }
        if (!empty($post['description'])) {
          echo "<p>".$post['description'].'</p>';
        }
        if(isset($_SESSION['uid']))  {
          $postid = $post['id'];
          $uid = $_SESSION['uid'];
          $likeSql = "SELECT*FROM groupPostlikes WHERE postid='$postid' AND uid='$uid'";
          $iliked = mysqli_query($conn, $likeSql);
          if(mysqli_num_rows($iliked) > 0) {
            echo '<button class="likeButtonLiked"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
          } else {
            echo '<button class="likeButton"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
          }
        } else {
          echo '<button class="likeButtonPlain"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
        }
        $commentSql = 'SELECT*FROM groupcomments WHERE postid=? ORDER BY commentdate';
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt,$commentSql)) {
          echo "Comments failed to load.";
          exit();
        } else {
          mysqli_stmt_bind_param($stmt,"s",$post['id']);
          mysqli_stmt_execute($stmt);
          $commentResult = mysqli_stmt_get_result($stmt);
          $commentCheck = mysqli_num_rows($commentResult);
          if($commentCheck > 5){
            echo '<h4 class="commButton">View '.$commentCheck.' Comments: </h4>';
            if ($post['type'] != 'text'  && $refresh) {
              echo '<h4 class="heart" data-linktype="'.$post['type'].'" data-linkid="'.$post['link'].'">Save to Spotify</h4>';
            }
            echo '<h5>Posted by: <a href="user.php?uid='.$post['poster'].'">'.$post['poster'].'</a> on '.date("M j, g:i a",strtotime($post['postdate']) + $tz_offset).'</h5>
            </div><section id="comments'.$post['id'].'" style="display: none">';
            for ($i=0; $i<5; $i++) {
              $comment = mysqli_fetch_assoc($commentResult);
              echo '<div id="comment'.$comment['id'].'"><p>'.$comment['commenttext'].'<br><span>Posted by: <a href="user.php?uid='.$comment['commenter'].'">'.$comment['commenter'].'</a> on '.date("M j, g:i a",strtotime($comment['commentdate']) + $tz_offset).'</span></p>';
              if (isset($_SESSION['uid']) && $_SESSION['uid'] == $comment['commenter']) { //Red X to delete post if it is the users post
                echo '<h4 class="deleteComment" data-commentid="'.$comment['id'].'">Delete Comment</h4>';
              }
              echo '</div>';

            }
            echo '<button id="loadMoreCommButton'.$post['id'].'" class="loadMoreCommButton" data-comments="5" data-postid="'.$post['id'].'">Load more comments</button>';
          } elseif($commentCheck >= 1){
            echo '<h4 class="commButton">View '.$commentCheck.' Comment'; if($commentCheck > 1){echo 's';} echo ': </h4>';
            if ($post['type'] != 'text' && $refresh) {
              echo '<h4 class="heart" data-linktype="'.$post['type'].'" data-linkid="'.$post['link'].'">Save to Spotify</h4>';
            }
            echo '<h5>Posted by: <a href="user.php?uid='.$post['poster'].'">'.$post['poster'].'</a> on '.date("M j, g:i a",strtotime($post['postdate']) + $tz_offset).'</h5>
            </div><section id="comments'.$post['id'].'" style="display: none">';
            while ($comment = mysqli_fetch_assoc($commentResult)) {
              echo '<div id="comment'.$comment['id'].'"><p>'.$comment['commenttext'].'<br><span>Posted by: <a href="user.php?uid='.$comment['commenter'].'">'.$comment['commenter'].'</a> on '.date("M j, g:i a",strtotime($comment['commentdate']) + $tz_offset).'</span></p>';
              if (isset($_SESSION['uid']) && $_SESSION['uid'] == $comment['commenter']) { //Red X to delete post if it is the users post
                echo '<h4 class="deleteComment" data-commentid="'.$comment['id'].'">Delete Comment</h4>';
              }
              echo '</div>';
            }
          } else {
            if (isset($_SESSION['uid'])) {
              echo '<h4 class="commButton">Add comment</h4>';
              if ($post['type'] != 'text' && $refresh) {
                echo '<h4 class="heart" data-linktype="'.$post['type'].'" data-linkid="'.$post['link'].'">Save to Spotify</h4>';
              }
              echo '<h5>Posted by: <a href="user.php?uid='.$post['poster'].'">'.$post['poster'].'</a> on '.date("M j, g:i a",strtotime($post['postdate']) + $tz_offset).'</h5>
              </div><section id="comments'.$post['id'].'" style="display: none">';
            } else {
              if ($post['type'] != 'text' && $refresh) {
                echo '<h4 class="heart" data-linktype="'.$post['type'].'" data-linkid="'.$post['link'].'">Save to Spotify</h4>';
              }
              echo '<h5>Posted by: <a href="user.php?uid='.$post['poster'].'">'.$post['poster'].'</a> on '.date("M j, g:i a",strtotime($post['postdate']) + $tz_offset).'</h5>';
              echo '</div>';
            }
          }
          if (isset($_SESSION['uid'])) {
            echo '<textarea class="commentBox" id="commForm'.$post['id'].'" placeholder="&#10;    Add a comment..." name="text" rows="3" cols="20" required></textarea></section>';
          }
          echo '</div>'; //close off div of class main
        }
      }
      if ($resultCheck >= 8) {
        echo '<button class="loadMoreButton" data-groupid="'.$groupid.'">Load more posts</button>';
      }
    } else {
      echo '<div class="main"><div class="post" id="noPosts">  There are no posts in this group yet. Be the first to submit a post! ----> </div></div>';
    }
  }
} else {
echo '<p>Page load failure.</p>';
}
?>
      </div>
      <?php
      $sql = "SELECT uid FROM grouplikes WHERE groupid='$groupid' LIMIT 100";
      $result = mysqli_query($conn, $sql);
      $numMembers = mysqli_num_rows($result);
      echo '<div class="rightSidebar"><button id="postButton">Post</button><button id="memberButton" class="hover">Members ('.$numMembers.')</button><div id="postForm">';
      if (!empty($uid)) {
        echo '<select id="linkType" name="linkType">
          <option value="track" default>Song</option>
          <option value="album">Album</option>
          <option value="playlist">Playlist</option>
          <option value="artist">Artist</option>
        </select>';
        echo '<input id="postLink" name="link" type="text" autocomplete="off" placeholder="  Search Spotify here...">
        <ul id="searchResults" style="display: none;"></ul>
      <br><textarea id="postText" name="desc" rows="4" cols="20" placeholder="&#10;        Add a comment"></textarea><button data-groupid="'.$groupid.'" data-private="'.$private.'" id="postSubmitButton" class="postSubmitButton" >Submit</button>';
    } else {
      echo '<div>Create an account or log in to post in this group!</div>';
        if (!empty($_GET['error'])) {
          $error = $_GET['error'];
          if ($error == "wrongPassword") {
            echo '<p style="color: white;">Wrong password. Try again</p>';
          } elseif ($error == "noSuchUid") {
            echo '<p style="color: white;">User does not exist. Try again</p>';
          } elseif ($error == "invalidSql") {
            echo '<p style="color: white;">Something went wrong. Try again</p>';
          }
        }
        echo '<form id="login" action="includes/loginGroup.php" method="POST">
        <input type="hidden" name="groupid" value="'.$groupid.'" />
        <input id="mailuid" name="mailuid" type="text" placeholder="   Username or e-mail"';
      
        if (!empty($error) && !empty($_GET['uid']) && $error == "wrongPassword") {
          echo 'value='.$_GET['uid'];;
        } 
        echo ' required>
        <input id="pwd" name="pwd" type="password" placeholder="           Password" required>
        <button id="login-submit" name="login-submit" type="submit">Log in</button></form>
        <p id="register">New to groupslync? Click <a href="AccountSignUp.php">here</a> to create a new account</p>';
    }
    echo '</div>'; //Close postForm Div
    if (mysqli_num_rows($result) > 0) {
       echo '<div id="members"><ul class="groupMembers">';
       echo '<li><a class="hostName" href="user.php?uid='.$owner.'">'.$owner.'</a></li>'; //Host
       while($grouplike = mysqli_fetch_assoc($result)) {
         if ($grouplike['uid'] != $owner ) {
           echo '<li><a href="user.php?uid='.$grouplike['uid'].'">'.$grouplike['uid'].'</a></li>';
         }
       }
       echo '</ul>';
       if(!empty($uid)){
         if ($owner == $uid) {
           echo '<div id="invites">Invite others!';

           echo '<textarea id="inviteArea" rows="4" cols="20" placeholder="&#10;       Usernames,Emails..."></textarea>';
           echo '<textarea id="message" rows="4" cols="20" maxlength="1000" placeholder="&#10;     Message to send..."></textarea>';
           echo '<button id="inviteButton" data-groupid="'.$groupid.'" data-groupname="'.$groupname.'">Send Invites</button>';
           echo '</div>';
         }
       }
       echo '</div>'; //close off members div
     }
    echo '</div>'; //close off rightSidebar div
    if(empty($_COOKIE['cookies'])) {
      echo '<div id="cookies">This site uses cookies to improve your experience. Click<span id="cookieLink">here</span>to accept</div>';
    } 
    ?>
    </main>
  </body>
  <script src="js/group.js" type="text/javascript"></script>
  <script src="js/notifScript.js" type="text/javascript"></script>
</html>
