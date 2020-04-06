<?php
session_start();

require '../../dbh.php';

$groupid = $_POST['groupid'];
$link = trim($_POST['link']);
$poster = $_SESSION['uid'];
$private = $_POST['private'];
$desc = $_POST['desc'];
if(isset($_POST['title'])) {
  $title = $_POST['title'];
}
//link handling
if(!empty($_COOKIE['refresh_token'])) {
  $refresh = 1;
} else {
  $refresh = 0;
}

if( (empty($desc) && empty($link)) || empty($poster) || !is_numeric($private)) {
  echo '<h6>ERROR - Incomplete form.</h6>';
  exit();
}

if(empty($link)){
  $type = 'text';
  $title = $desc;
}
elseif(strpos($link,"https://open.spotify.com/track/") === 0){
  if(strpos($link,"?si=") == 0) {
    $link = substr($link, strpos($link, '/track/') + 7);
  } else {
    $link = substr($link, strpos($link, '/track/') + 7, strpos($link, '?si=') - strpos($link, "/track/") + 7);
  }
  $type = 'spotLink';
} elseif(strpos($link, 'https://open.spotify.com/playlist/') === 0){
  if(strpos($link,"?si=") == 0) {
    $link = substr($link, strpos($link, '/playlist/') + 10);
  } else {
    $link = substr($link, strpos($link,'/playlist/') + 10, strpos($link, '?si=') - strpos($link,"/playlist/") + 10);
  }
  $type = 'spotPlaylist';
} elseif(strpos($link, 'https://open.spotify.com/album/') === 0){
  if(strpos($link,"?si=") == 0) {
    $link = substr($link, strpos($link, '/album/') + 7);
  } else {
    $link = substr($link, strpos($link,'/album/') + 7, strpos($link, '?si=') - strpos($link,"/album/") + 7);
  }
  $type = 'spotAlbum';
} elseif(strpos($link, 'https://open.spotify.com/artist/') === 0){
  if(strpos($link,"?si=") == 0) {
    $link = substr($link, strpos($link, '/artist/') + 8);
  } else {
    $link = substr($link, strpos($link,'/artist/') + 8, strpos($link, '?si=') - strpos($link,"/artist/") + 8);
  }
  $type = 'spotArtist';
}  else {
  echo '<h6>ERROR - Invalid link.</h6>';
  exit();
}

$sql = "INSERT INTO groupposts (link, type, description, groupid, poster, private, title) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_stmt_init($conn);
if (!mysqli_stmt_prepare($stmt,$sql)) {
  echo '<h6>ERROR - Post submission failed. Please try again</h6>';
  exit();
} else {
  mysqli_stmt_bind_param($stmt,"sssssss", $link, $type, $desc, $groupid, $poster, $private, $title);
  mysqli_stmt_execute($stmt);
  $postid = $conn->insert_id;
  $sql = "UPDATE groups SET postcount=postcount+1 WHERE id=?";
  $stmt = mysqli_stmt_init($conn);
  if (!mysqli_stmt_prepare($stmt,$sql)) {
    exit();
  } else{
    mysqli_stmt_bind_param($stmt,"s", $groupid);
    mysqli_stmt_execute($stmt);
    echo '<div id="post'.$postid.'" data-groupid="'.$groupid.'" data-postid="'.$postid.'" data-poster="'.$poster.'" data-title="'.$title.'" class="main"><div class="post">';
    echo '<h4 class="deletePost">Delete Post</h4>';
    if ($type === 'spotLink') {
      echo '<iframe src="https://open.spotify.com/embed/track/'.$link.'" height="80px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
    } elseif($type === 'spotPlaylist'){
      echo '<iframe src="https://open.spotify.com/embed/playlist/'.$link.'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
    } elseif($type === 'spotAlbum'){
      echo '<iframe src="https://open.spotify.com/embed/album/'.$link.'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
    } elseif($type === 'spotArtist'){
      echo '<iframe src="https://open.spotify.com/embed/artist/'.$link.'" height="210px" frameborder="10%" allowtransparency="true" allow="encrypted-media"></iframe>';
    }
    if (!empty($desc)) {
      echo "<p>".$desc.'</p>';
    }
    $uid = $_SESSION['uid'];
    echo '<button class="likeButton"><span>0</span><i class="fas fa-thumbs-up"></i></button>';
    echo '<h4 class="commButton">Add comment</h4>';
    if ($type != 'text' && $refresh) {
      echo '<h4 class="heart" data-linktype="'.$type.'" data-linkid="'.$link.'">Save to Spotify</h4>';
    }
    echo '<h5>Posted by: You just now</h5>
    </div><section id="comments'.$postid.'" style="display: none">';
    echo '<textarea class="commentBox" id="commForm'.$postid.'" placeholder="&#10;    Add a comment..." name="text" rows="3" cols="20" required></textarea></section>';
    echo '</div>'; //close off div of class main
    $sql = "UPDATE grouplikes SET unseenposts = unseenposts+1 WHERE groupid=? AND uid != ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt,$sql)) {
      exit();
    } else{
      mysqli_stmt_bind_param($stmt,"ss", $groupid, $poster);
      mysqli_stmt_execute($stmt);
    }
  }
}
mysqli_stmt_close($stmt);
