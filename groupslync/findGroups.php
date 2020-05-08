<?php
  session_start();
  require '../dbh.php';
  require './commonFunctions.php';
 ?>

<!DOCTYPE html>

<html lang="en">
  <head>
    <title>Group Search</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/findGroups.css" type="text/css">
    <link rel="stylesheet" href="css/nav.css" type="text/css">
    <script src="https://kit.fontawesome.com/8b7394b262.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  </head>
  <body class="findGroups" id="findGroups">
     <nav>
       <ul>
         <?php
         if (isset($_SESSION['uid'])) {
          $uid = $_SESSION['uid'];
           echo '<li class="mainli"><a href="index.php"><img id="logo" src="includes/logo.png"><span id="home">Home</span></a></li>
           <li class="mainli"><a href="myGroups.php">My Groups</a></li>
           <li class="mainli"><a href="findGroups.php" class="currentPage">Find Group</a></li>
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
           <li class="mainli"><a href="findGroups.php" class="currentPage"">Find Group</a></li>
           <li class="mainli"><a href="AccountSignUp.php">Sign Up</a></li>
           <li class="mainli"><a href="connection.php"><img id="spotifyButton" src="includes/Spotify_Icon_RGB_Green.png"></a></li>';
         }
          ?>
       </ul>
     </nav>
     <main>
      <h1>Group Search</h1>
        <br>
        <form action="findGroups.php" method="POST">
          <select name="genre">
            <option value=""> - Genre - </option>
            <option value="Variety">Variety</option>
            <option value="Hip-hop">Hip-hop/Rap</option>
            <option value="Pop">Pop</option>
            <option value="Indie">Indie/Alternative</option>
            <option value="R&B">R&B</option>
            <option value="Rock">Rock</option>
            <option value="Jazz">Jazz</option>
            <option value="Metal">Metal</option>
            <option value="Country">Country</option>
            <option value="Electronic">Electronic</option>
          </select>
          <br>
          <input id="searchBar" name="searchBar" type="text" title="Letters and numbers">
          <br>
          <input type="submit" id="search" name="groupSearchSubmit" value="Search">
        </form>
        <div class="groupSearchResults">
           <?php
           if (isset($_POST['groupSearchSubmit'])) {
             $search = mysqli_real_escape_string($conn, $_POST['searchBar']);
             if (!empty($_POST['genre'])) {
               $genre = $_POST['genre'];
               $sql = "SELECT*FROM groups WHERE private = 0 AND genre='$genre' AND name LIKE '%$search%' ORDER BY likecount DESC LIMIT 10";
             } else {
               $sql = "SELECT*FROM groups WHERE private = 0 AND name LIKE '%$search%' ORDER BY likecount DESC LIMIT 10";
             }
             $result = mysqli_query($conn, $sql);
             $queryResult = mysqli_num_rows($result);
             if (isset($_SESSION['uid'])) { //Include like star
               if ($queryResult > 0) {
                 $uid = $_SESSION['uid'];
                 echo '<table id="table"><tr><th></th><th>Group Name</th><th>Owner</th><th>Genre</th><th>Likes</th><th>Posts</th></tr>';
                 while ($group = mysqli_fetch_assoc($result)) {
                   $groupid = $group['id'];
                   $sql = "SELECT*FROM grouplikes WHERE uid='$uid' AND groupid='$groupid'";
                   $result2 = mysqli_query($conn, $sql);
                   if (!mysqli_fetch_assoc($result2)) {
                      echo '<tr><td><i class="far fa-star" data-title="'.$group['name'].'" data-host="'.$group['owner'].'" data-groupid="'.$groupid.'"></i></a></td><td><a href="group.php?id='.$groupid.'">'.$group['name'].'</a></td><td>'.$group['owner'].'</td><td>'.$group['genre'].'</td><td>'.$group['likecount'].'</td><td>'.$group['postcount'].'</td></tr>';
                    } else {
                      echo '<tr><td></td><td><a href="group.php?id='.$groupid.'">'.$group['name'].'</a></td><td>'.$group['owner'].'</td><td>'.$group['genre'].'</td><td>'.$group['likecount'].'</td><td>'.$group['postcount'].'</td></tr>';
                    }
                 }
                 echo '</table>';
               } else {
                 echo "<p>There are no groups matching your search. Please try again.</p>";
               }
             } else { //no like star
               if ($queryResult > 0) {
                 echo '<table id="table"><tr><th></th><th>Group Name</th><th>Owner</th><th>Likes</th><th>Posts</th></tr>';
                 while ($group = mysqli_fetch_assoc($result)) {
                   echo '<tr><td><a href="group.php?id='.$group['id'].'">'.$group['name'].'</a></td><td>'.$group['owner'].'</td><td>'.$group['genre'].'</td><td>'.$group['likecount'].'</td><td>'.$group['postcount'].'</td></tr>';
                 }
                 echo '</table>';
               } else {
                 echo "<p>There are no groups matching your search. Please try again.</p>";
               }
              }
           }
            ?>
        </div>
    </main>
  </body>
  <script src="js/findGroups.js" type="text/javascript"></script>
  <script src="js/notifScript.js" type="text/javascript"></script>
</html
