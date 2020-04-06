$(function() {
  const postFeed = document.getElementById("postFeed"),
    type = $(postFeed).data("type");
  postFeed.addEventListener("click", event => {
    const target = event.target,
      eventClass = event.target.className;
    if (eventClass == "commButton") {
      const postid = $(target)
          .parent()
          .parent()
          .data("postid"),
        commentSection = document.getElementById("comments" + postid);
      if (commentSection.style.display == "none") {
        commentSection.style.display = "block";
        target.innerHTML = "Hide " + target.innerHTML.substr(4);
      } else {
        commentSection.style.display = "none";
        target.innerHTML = "View " + target.innerHTML.substr(4);
      }
    } else if (eventClass == "likeButton") {
      const postid = $(target)
        .parent()
        .parent()
        .data("postid");
      const poster = $(target)
        .parent()
        .parent()
        .data("poster");
      const title = $(target)
        .parent()
        .parent()
        .data("title");
      const spanElement = target.firstChild;
      $.post({
        url: `includes/like${type}Post.php`,
        data: { postid, poster, title },
        success: function() {
          const likeCount = parseInt(spanElement.innerHTML);
          spanElement.innerHTML = likeCount + 1;
          spanElement.style.color = "white";
          target.className = "likeButtonLiked";
        },
        error: function() {
          alert("An error occurred with this like.");
        }
      });
    } else if (eventClass == "likeButtonLiked") {
      const postid = $(target)
        .parent()
        .parent()
        .data("postid");
      const poster = $(target)
        .parent()
        .parent()
        .data("poster");
      const title = $(target)
        .parent()
        .parent()
        .data("title");
      const span = target.firstChild;
      $.post({
        url: `includes/unlike${type}Post.php`,
        data: { postid, poster, title },
        success: function() {
          const likeCount = parseInt(span.innerHTML);
          span.innerHTML = likeCount - 1;
          span.color = "black";
          target.className = "likeButton";
        },
        error: function() {
          alert("An error occurred with this unlike.");
        }
      });
    } else if (eventClass == "heart") {
      const linkid = $(target).data("linkid"),
        linktype = $(target).data("linktype"),
        refresh_token = $.cookie("refresh_token");
      $.ajax({
        //First get access_token using refresh token
        url: "https://musicauthbackend.herokuapp.com/refresh_token/",
        data: { refresh_token },
        success: function(tokenInfo) {
          const access_token = "Bearer " + tokenInfo.access_token;
          if (linktype == "spotLink") {
            $.ajax({
              type: "PUT",
              url: "https://api.spotify.com/v1/me/tracks",
              data: JSON.stringify({ ids: [linkid] }),
              headers: {
                Authorization: access_token,
                "Content-Type": "application/json"
              },
              success: function() {
                target.className = "heartLiked";
                target.innerText = "Saved!";
              },
              error: function() {
                alert("Unable to like song. You may need to re-authenticate with Spotify.");
              }
            });
          } else if (linktype == "spotAlbum") {
            $.ajax({
              type: "PUT",
              url: "https://api.spotify.com/v1/me/albums",
              data: JSON.stringify({ ids: [linkid] }),
              headers: {
                Authorization: access_token,
                "Content-Type": "application/json"
              },
              success: function() {
                target.className = "heartLiked";
                target.innerText = "Saved!";
              },
              error: function() {
                alert("Unable to like album. You may need to re-authenticate with Spotify.");
              }
            });
          } else if (linktype == "spotPlaylist") {
            $.ajax({
              type: "PUT",
              url: "https://api.spotify.com/v1/playlists/" + linkid + "/followers",
              headers: {
                Authorization: access_token,
                "Content-Type": "application/json"
              },
              success: function() {
                target.className = "heartLiked";
                target.innerText = "Saved!";
              },
              error: function() {
                alert("Unable to like playlist. You may need to re-authenticate with Spotify.");
              }
            });
          } else if (linktype == "spotArtist") {
            $.ajax({
              type: "PUT",
              url: "https://api.spotify.com/v1/me/following?type=artist&ids=" + linkid,
              headers: {
                Authorization: access_token,
                "Content-Type": "application/json"
              },
              success: function() {
                target.className = "heartLiked";
                target.innerText = "Saved!";
              },
              error: function() {
                alert("Unable to like artist. You may need to re-authenticate with Spotify.");
              }
            });
          }
        }
      });
    } else if (eventClass == "deleteComment" || eventClass == "deleteComment temp") {
      const commentid = $(target).data("commentid");
      if (Confirm("comment")) {
        $.post({
          url: `includes/remove${type}Comment.php`,
          data: { commentid },
          success: function() {
            target.remove();
            $("#comment" + commentid).slideUp(1200);
          },
          error: function() {
            alert("Unable to delete comment.");
          }
        });
      }
    } else if (eventClass == "deletePost") {
      const postid = $(target)
        .parent()
        .parent()
        .data("postid");
      if (Confirm("post")) {
        $.post({
          url: `includes/remove${type}Post.php`,
          data: { postid },
          success: function() {
            $("#post" + postid).slideUp(1200);
          },
          error: function() {
            alert("Unable to delete post.");
          }
        });
      }
    } else if (eventClass == "loadMoreCommButton") {
      const postId = $(target).data("postid"),
        numComments = parseInt($(target).attr("data-comments"));
      $.post({
        url: `includes/loadMore${type}Comments.php`,
        data: { numComments, postId },
        success: function(data) {
          target.remove();
          $(".temp").remove();
          if ($("#commForm" + postId).length) $(data).insertBefore($("#commForm" + postId));
          else $("#comments" + postId).append(data);
          target.setAttribute("data-comments", numComments + 5);
        },
        error: function() {
          alert("Unable to load more comments.");
        }
      });
    }
  });
  postFeed.addEventListener("keydown", event => {
    const target = event.target;
    if (event.key === "Enter" && target.className == "commentBox") {
      const postid = $(target)
        .parent()
        .parent()
        .data("postid");
      const poster = $(target)
        .parent()
        .parent()
        .data("poster");
      const title = $(target)
        .parent()
        .parent()
        .data("title");
      const commentText = $("#commForm" + postid);
      $.post({
        url: `includes/new${type}Comment.php`,
        data: { text: commentText.val(), postid, poster, title },
        success: function(data) {
          if ($("#loadMoreCommButton" + postid).length)
            $(data)
              .hide()
              .insertBefore($("#loadMoreCommButton" + postid))
              .fadeIn(1500);
          else
            $(data)
              .hide()
              .insertBefore($("#commForm" + postid))
              .fadeIn(1500);
          commentText.val("");
        },
        error: function() {
          if ($("#loadMoreCommButton" + postid).length) $("<p>Comment failed to post.</p>").insertBefore($("#loadMoreCommButton" + postid));
          else $("<p>Comment failed to post.</p>").insertBefore($("#commForm" + postid));
          commentText.val("");
        }
      });
    }
  });
  const Confirm = type => {
    let x;
    if (type === "post") x = confirm("Are you sure you want to delete this post? You cannot undo this action.");
    else if (type === "comment") x = confirm("Are you sure you want to delete this comment?");
    return x;
  };
});
