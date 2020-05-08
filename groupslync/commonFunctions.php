<?php

function loadNotifications($uid, $conn) {
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
        echo '<li id="bell" class="mainli"><i class="fas fa-bell"></i><ul id="notifications">';
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
}