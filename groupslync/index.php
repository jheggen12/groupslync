<?php
  session_start();
  require '../dbh.php';
  require './commonFunctions.php';
  
  if(isset($_SESSION['uid'])) {
    $uid = $_SESSION['uid'];
  }
  if(!empty($_GET['genre'])) {
    $genre = $_GET['genre'];
  } else {
    $genre = '';
  }
  if(!empty($_GET['sort'])) {
    $sort = $_GET['sort'];
  } else {
    $sort = '';
  }
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <title>groupslync</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/index.css" type="text/css">
    <link rel="stylesheet" href="css/feed.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/jquery.cookie.js"></script>
  </head>
  <body class="body">
     <div id="scrollBlock"></div>
     <nav>
       <ul>
         <?php
         if (!empty($uid)) {
           echo '<li class="mainli"><a href="index.php" class="currentPage"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
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
           echo '<li class="mainli"><a href="index.php" class="currentPage"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="findGroups.php">Find Group</a></li>
           <li class="mainli"><a href="AccountSignUp.php">Sign Up</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>';
         }
          ?>
       </ul>
     </nav>
     <main class="feed">
        <?php
        echo '<div class="leftSidebar">';
        if (!empty($genre)) {
          echo '<h2>'.$genre.'</h2>';
        } else {
          echo '<h2>Home</h2>';
        }
        if (!empty($genre)){
          echo '<select id="feedSort" onchange="SortChange(\''.$genre.'\');">';
          if (!empty($sort)) {
            echo '<option value="">Hot</option>
            <option value="new" selected>New</option>';
          }
          else {
            echo '<option value="" selected>Top</option>
            <option value="new">New</option>';
          }
        } else {
          echo '<select id="feedSort" onchange="SortChange();">';
          if (!empty($sort)) {
            echo '<option value="">Top</option>
            <option value="new" selected>New</option>';
          }
          else {
            echo '<option value="" selected>Top</option>
            <option value="new">New</option>';
          }
        }
        echo '</select>';
          ?>
          <div id="genres">
            <a href="index.php"><span>All</span></a>
            <a class="rightGenre" href="index.php?genre=Hip-hop"><span>Hip-hop</span></a>
            <a href="index.php?genre=Pop"><span>Pop</span></a>
            <a class="rightGenre" href="index.php?genre=Indie"><span>Indie</span></a>
            <a href="index.php?genre=R%26B"><span>R & B</span></a>
            <a class="rightGenre" href="index.php?genre=Rock"><span>Rock</span></a>
            <a href="index.php?genre=Jazz"><span>Jazz</span></a>
            <a class="rightGenre" href="index.php?genre=Metal"><span>Metal</span></a>
            <a href="index.php?genre=Country"><span>Country</span></a>
            <a class="rightGenre" href="index.php?genre=Electronic"><span>Electronic</span></a>
          </div>
        </div>
      <div class="postAndComments" id="homeFeed">
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
if (!empty($genre)) {
  if (!empty($sort) && $sort == 'new') {
    $sql = 'SELECT*FROM publicposts WHERE genre=? ORDER BY postdate DESC LIMIT 8';
  } else {
    $sql = 'SELECT*FROM publicposts WHERE genre=? AND postdate > DATE_SUB(CURDATE(), INTERVAL 28 DAY) ORDER BY likes DESC LIMIT 8';
  }
} else {
  if (!empty($sort) && $sort == 'new') {
    $sql = 'SELECT*FROM publicposts ORDER BY postdate DESC LIMIT 8';
  } else {
    $sql = 'SELECT*FROM publicposts WHERE postdate > DATE_SUB(CURDATE(), INTERVAL 28 DAY) ORDER BY likes DESC LIMIT 8';
  }
}
$stmt = mysqli_stmt_init($conn);
if(!mysqli_stmt_prepare($stmt,$sql)) {
  echo "Page load failures.";
  exit();
} else {
  if (!empty($genre)){
    mysqli_stmt_bind_param($stmt,"s",$genre);
  }
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);
  $resultCheck = mysqli_num_rows($result);
  if ($resultCheck > 0) {
    while ($post = mysqli_fetch_assoc($result)) {
      echo '<div id="post'.$post['id'].'" data-postid="'.$post['id'].'" data-poster="'.$post['poster'].'" data-title="'.$post['title'].'" class="main"><div class="post">';
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
      if(!empty($uid))  {
        $postid = $post['id'];
        $likeSql = "SELECT*FROM publicPostlikes WHERE postid='$postid' AND uid='$uid'";
        $iliked = mysqli_query($conn, $likeSql);
        if(mysqli_num_rows($iliked) > 0) {
          echo '<button class="likeButtonLiked"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
        } else {
          echo '<button class="likeButton"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
        }
      } else {
        echo '<button class="likeButtonPlain"><span>'.$post['likes'].'</span> <i class="fas fa-thumbs-up"></i></button>';
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
          if (!empty($uid)) {
            echo '<h4 class="commButton">Add comment</h4>';
            if ($post['type'] != 'text' && $refresh) {
              echo '<h4 class="heart" data-linktype="'.$post['type'].'" data-linkid="'.$post['link'].'">Save to Spotify</h4>';
            }
            echo '<h5>Posted by: <a href="user.php?uid='.$post['poster'].'">'.$post['poster'].'</a> on '.date("M j, g:i a",strtotime($post['postdate']) + $tz_offset).'</h5>
            </div><section id="comments'.$post['id'].'" style="display: none">';
          } else {
            if ($post['type'] != 'text'  && $refresh) {
              echo '<h4 class="heart" data-linktype="'.$post['type'].'" data-linkid="'.$post['link'].'">Save to Spotify</h4>';
            }
            echo '<h5>Posted by: <a href="user.php?uid='.$post['poster'].'">'.$post['poster'].'</a> on '.date("M j, g:i a",strtotime($post['postdate']) + $tz_offset).'</h5>';
            echo '</div>';
          }
        }
        if (!empty($uid)) {
          echo '<textarea class="commentBox" id="commForm'.$post['id'].'" placeholder="&#10;    Add a comment..." name="text" rows="3" cols="20" required></textarea></section>';
        }
        echo '</div>'; //close off div of class main
      }
    }
    if ($resultCheck >= 8) {
      echo '<button class="loadMoreButton" data-sort="'.$sort.'" data-genre="'.$genre.'">Load more posts</button>';
    }
  } else {
    echo '<div class="main"><div class="post" id="noPosts">  There are no posts in this feed yet.<br><br> Be the first to submit a post! </div></div>';
  }
}
?>
</div>
<?php
echo '<div class="rightSidebar">';
echo '<div id="postForm">';
if (!empty($uid)) {
  echo '<h1>Post</h1>';
}
if (!empty($uid)) {
  echo '<select id="linkType" name="linkType">
    <option value="track" default>Song</option>
    <option value="album">Album</option>
    <option value="playlist">Playlist</option>
    <option value="artist">Artist</option>
  </select>
  <select id="linkGenre" name="linkGenre">';
  $genreArray = ["Hip-hop","Pop","Indie","R&B","Rock","Jazz","Metal","Country","Electronic"];
  if (!empty($genre)){
    echo '<option value="'.$genre.'" default>'.$genre.'</option>';
    $genreArray = array_filter($genreArray, function($item) {
      return $item != $_GET['genre'];
    });
  } else {
    echo '<option value="" default>- Genre -</option>';
  }
  foreach($genreArray as $item){
    echo '<option value="'.$item.'">'.$item.'</option>';
  }
  echo '</select>';
  echo '<input id="postLink" name="link" type="text" autocomplete="off" placeholder="  Search Spotify here...">
  <ul id="searchResults" style="display: none;"></ul><br>';
  echo '<textarea id="postText" name="desc" rows="4" cols="20" placeholder="&#10;     Add a comment.." required></textarea><button id="postSubmitButton" class="postSubmitButton">Submit!</button>';
} else {
echo '<div><p>Welcome to groupslync. Log in and connect your Spotify account to start finding and sharing music!</p>
  </div>';
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
  echo '<form id="login" action="includes/login.php" method="POST">
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
if ($resultCheck > 0) {
  if (isset($_COOKIE['refresh_token'])) {
    echo '<button class="rightButton" id="playlistButton" data-genre="'.$genre.'" data-sort="'.$sort.'">Create playlist from songs in feed</button>';
  } else {
    echo '<a href="connection.php"><button class="rightButton">Login with Spotify to create a playlist</button></a>';
  }
}
echo '</div>'; //close off rightSidebar div
if(empty($_COOKIE['cookies'])) {
  echo '<div id="cookies">This site uses cookies to improve your experience. Click<span id="cookieLink">here</span>to accept</div>';
} elseif (!isset($_COOKIE['refresh_token'])) {
  echo '<div id="spotLogin">Login with Spotify to improve your browsing experience. Click <a style="color: blue;" href="connection.php">here</a> to login.</div>';
}
?>
     </div>
   </main>
  </body>
  <script type="module" src="js/index.js" type="text/javascript"></script>
  <script src="js/notifScript.js" type="text/javascript"></script>
<script>
function SortChange(value) {
  let sort = $('#feedSort').val();
  if(value) {
    if(sort) {
      window.location.href = 'index.php?genre=' + value + '&sort=new';
    } else {
      window.location.href = "index.php?genre=" + value;
    }
  } else {
    if(sort) {
      window.location.href = "index.php?sort=new";
    } else {
      window.location.href = 'index.php';
    }
  }
}
</script>
</html>