<?php
session_start();
require '../../dbh.php';

$postNewCount = $_POST['numPosts'];
$genre = $_POST['genre'];
$sort = $_POST['sort'];
if(!empty($_COOKIE['refresh_token'])) {
  $refresh = 1;
} else {
  $refresh = 0;
}
if(!empty($_COOKIE['tzo'])) {
  $tz_offset = $_COOKIE['tzo'];
} else {
  $tz_offset = 3600;
}
if (!empty($genre)) {
  if (!empty($sort) && $sort == 'new') {
    $sql = 'SELECT*FROM publicposts WHERE genre=? ORDER BY postdate DESC LIMIT 8 OFFSET '.$postNewCount;
  } else {
    $sql = 'SELECT*FROM publicposts WHERE genre=? AND postdate > DATE_SUB(CURDATE(), INTERVAL 28 DAY) ORDER BY likes DESC LIMIT 8 OFFSET '.$postNewCount;
  }
} else {
  if (!empty($sort) && $sort == 'new') {
    $sql = 'SELECT*FROM publicposts ORDER BY postdate DESC LIMIT 8 OFFSET '.$postNewCount;
  } else {
    $sql = 'SELECT*FROM publicposts WHERE postdate > DATE_SUB(CURDATE(), INTERVAL 28 DAY) ORDER BY likes DESC LIMIT 8 OFFSET '.$postNewCount;
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
        if (isset($_SESSION['uid'])) { //Red X to delete post if it is the users post
          if ($_SESSION['uid'] == $post['poster']) {
            echo '<h4 class="deletePost">Delete Post</h4>';
          }
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
          $ilikeSql = "SELECT*FROM publicPostlikes WHERE postid='$postid' AND uid='$uid'";
          $iliked = mysqli_query($conn, $ilikeSql);
          if(mysqli_num_rows($iliked) > 0) {
            echo '<button class="likeButtonLiked"><span>'.$post['likes'].'</span> <i id="likeIcon'.$post['id'].'" class="fas fa-thumbs-up"></i></button>';
          } else {
            echo '<button class="likeButton"><span>'.$post['likes'].'</span> <i id="likeIcon'.$post['id'].'" class="fas fa-thumbs-up"></i></button>';
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
            if (isset($_SESSION['uid'])) {
              echo '<h4 class="commButton">Add comment</h4>';
              if ($post['type'] != 'text' && $refresh) {
                echo '<h4 class="heart" data-linktype="'.$post['type'].'" data-linkid="'.$post['link'].'">Save to Spotify</h4>';
              }
              echo '<h5>Posted by: <a href="user.php?uid='.$post['poster'].'">'.$post['poster'].'</a> on '.date("M j, g:i a",strtotime($post['postdate']) + $tz_offset).'</h5>
              </div><section id="comments'.$post['id'].'" style="display: none">';
            } else {
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
    echo '<button class="loadMoreButton" data-sort="'.$sort.'" data-genre="'.$genre.'">Load more posts</button>';
  } else {
    echo '<div class="main"><div>There are no more posts to load. Be the first to submit a post! ----> </div></div>';
  }
}
  ?>
