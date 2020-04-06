<?php
  session_start();
  require '../dbh.php';
  if(isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
  } else {
    die();
  }
  if(!empty($_GET['type'])) {
    if ($_GET['type'] == 'public') {
        $type = 'Public';
    } elseif ($_GET['type'] == 'group') {
        $type = 'Group';
    }
  } else {
    die();
  }
  if(!empty($_GET['id'])) {
    $postid = $_GET['id'];
  } else {
    die();
  }
?>

<!DOCTYPE html>

<html lang="en">
  <head>
    <title><?php echo $type?> Post</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/index.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/jquery.cookie.js"></script>
  </head>
  <body class="body">
     <nav>
       <ul>
         <?php
         if (!empty($uid)) {
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
            if(!mysqli_stmt_prepare($stmt, $notifSql)) {
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
    
     <main class="home">
<?php
if(!empty($_COOKIE['tzo'])) {
  $tz_offset = $_COOKIE['tzo'];
} else {
  $tz_offset = 3600;
}
if(!empty($_COOKIE['refresh_token'])) {
  $refresh = 1;
} else {
  $refresh = 0;
}
if ($type == 'Public') {
  $sql = 'SELECT*FROM publicposts WHERE id=? LIMIT 1';
} elseif($type == 'Group') {
  $sql = 'SELECT*FROM groupposts WHERE id=? LIMIT 1';
} else {
  die();
}

$stmt = mysqli_stmt_init($conn);
if(!mysqli_stmt_prepare($stmt,$sql)) {
  echo "Page load failures.";
  die();
} else {
    mysqli_stmt_bind_param($stmt,"s",$postid);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $resultCheck = mysqli_num_rows($result);
    if ($resultCheck > 0) {
        $post = mysqli_fetch_assoc($result);
        echo '<div class="postAndComments" id="postFeed" data-type="'.$type.'"><div id="post'.$post['id'].'" data-postid="'.$post['id'].'" data-poster="'.$post['poster'].'" data-title="'.$post['title'].'" class="main"><div class="post">';
        if (!empty($uid) && $uid == $post['poster']) { //Red X to delete post if it is the users post
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
        if($type == 'Public')  {
          $postid = $post['id'];
          $likeSql = "SELECT*FROM publicPostlikes WHERE postid='$postid' AND uid='$uid'";
          $iliked = mysqli_query($conn, $likeSql);
          if(mysqli_num_rows($iliked) > 0) {
            echo '<button class="likeButtonLiked"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
          } else {
            echo '<button class="likeButton"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
          }
          $commentSql = 'SELECT*FROM publiccomments WHERE postid=? ORDER BY commentdate';
          $stmt = mysqli_stmt_init($conn);
          if(!mysqli_stmt_prepare($stmt,$commentSql)) {
            echo "Comments failed to load.";
            exit();
          } else {
            mysqli_stmt_bind_param($stmt,"s",$post['id']);
            mysqli_stmt_execute($stmt);
            $commentResult = mysqli_stmt_get_result($stmt);
            $commentCheck = mysqli_num_rows($commentResult);
          }
        } elseif ($type == 'Group') {
          $postid = $post['id'];
          $likeSql = "SELECT*FROM groupPostlikes WHERE postid='$postid' AND uid='$uid'";
          $iliked = mysqli_query($conn, $likeSql);
          if(mysqli_num_rows($iliked) > 0) {
            echo '<button class="likeButtonLiked"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
          } else {
            echo '<button class="likeButton"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
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
          }
        }
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
          echo '<h4 class="commButton">View '.$commentCheck.' Comment'; if($commentCheck>1){echo 's';} echo ': </h4>';
          if ($post['type'] != 'text'  && $refresh) {
            echo '<h4 class="heart" data-linktype="'.$post['type'].'" data-linkid="'.$post['link'].'">Save to Spotify</h4>';
          }
          echo '<h5>Posted by: <a href="user.php?uid='.$post['poster'].'">'.$post['poster'].'</a> on '.date("M j, g:i a",strtotime($post['postdate']) + $tz_offset).'</h5>
          </div><section id="comments'.$post['id'].'" style="display: none">';
          while ($comment = mysqli_fetch_assoc($commentResult)) {
            echo '<div id="comment'.$comment['id'].'"><p>'.$comment['commenttext'].'<br><span>Posted by: <a href="user.php?uid='.$comment['commenter'].'">'.$comment['commenter'].'</a> on '.date("M j, g:i a",strtotime($comment['commentdate']) + $tz_offset).'</span></p>';
            if (!empty($uid) && $uid == $comment['commenter']) { //Red X to delete post if it is the users post
              echo '<h4 class="deleteComment" data-commentid="'.$comment['id'].'">Delete Comment</h4>';
            }
            echo '</div>';
          }
        } else {
          echo '<h4 class="commButton">Add comment</h4>';
          if ($post['type'] != 'text' && $refresh) {
            echo '<h4 class="heart" data-linktype="'.$post['type'].'" data-linkid="'.$post['link'].'">Save to Spotify</h4>';
          }
          echo '<h5>Posted by: <a href="user.php?uid='.$post['poster'].'">'.$post['poster'].'</a> on '.date("M j, g:i a",strtotime($post['postdate']) + $tz_offset).'</h5>
          </div><section id="comments'.$post['id'].'" style="display: none">';
        }
        echo '<textarea class="commentBox" id="commForm'.$post['id'].'" placeholder="&#10;    Add a comment..." name="text" rows="3" cols="20" required></textarea></section>';
        echo '</div>'; //close off div of class main
    } else {
      echo '<div class="main"><div class="post" id="noPosts">  This post does not exist.</div></div>';
    }
}
?>
</div>
     </div>
   </main>
  </body>
  <script src="js/post.js" type="text/javascript"></script>
  <script src="js/notifScript.js" type="text/javascript"></script>
</html>