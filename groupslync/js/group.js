import {
  refreshSpotifyToken,
  getBasicTokenFromSpotify,
  searchRequest,
  getBasicAuthFromHeroku,
  getUserIdFromSpotify,
  createSpotifyPlaylist,
  addSongsToPlaylist,
  addTzoCookie
} from "./commonFunctions.js";

$(function () {
  const groupFeed = document.getElementById("groupFeed");
  let numPosts = 8,
    auth,
    basic_token;
  const cookieLink = document.getElementById("cookieLink");
  if (cookieLink) {
    cookieLink.addEventListener("click", addTzoCookie );
  } //cookies pop-up
  groupFeed.addEventListener("click", (event) => {
    const target = event.target,
      eventClass = event.target.className;
    if (eventClass == "commButton") {
      const postid = $(target).parent().parent().data("postid"),
        commentSection = document.getElementById("comments" + postid);
      if (commentSection.style.display == "none") {
        commentSection.style.display = "block";
        target.innerHTML = "Hide " + target.innerHTML.substr(4);
      } else {
        commentSection.style.display = "none";
        target.innerHTML = "View " + target.innerHTML.substr(4);
      }
    } else if (eventClass == "likeButton") {
      const postid = $(target).parent().parent().data("postid");
      const poster = $(target).parent().parent().data("poster");
      const title = $(target).parent().parent().data("title");
      const spanElement = target.firstChild;
      $.post({
        url: "includes/likeGroupPost.php",
        data: { postid, poster, title },
        success: function () {
          const likeCount = parseInt(spanElement.innerHTML);
          spanElement.innerHTML = likeCount + 1;
          spanElement.style.color = "white";
          target.className = "likeButtonLiked";
        },
        error: function () {
          alert("An error occurred with this like.");
        },
      });
    } else if (eventClass == "likeButtonLiked") {
      const postid = $(target).parent().parent().data("postid");
      const poster = $(target).parent().parent().data("poster");
      const title = $(target).parent().parent().data("title");
      const spanElement = target.firstChild;
      $.post({
        url: "includes/unlikeGroupPost.php",
        data: { postid, poster, title },
        success: function () {
          const likeCount = parseInt(spanElement.innerHTML);
          spanElement.innerHTML = likeCount - 1;
          spanElement.style.color = "black";
          target.className = "likeButton";
        },
        error: function () {
          alert("An error occurred with this unlike.");
        },
      });
    } else if (eventClass == "heart") {
      const linkid = $(target).data("linkid"),
        linktype = $(target).data("linktype"),
        refresh_token = $.cookie("refresh_token");
      $.ajax({
        //Adds the songs to the Spotify playlist
        url: "https://musicauthbackend.herokuapp.com/refresh_token/",
        data: { refresh_token },
        success: function (data) {
          let access_token = "Bearer " + data.access_token;
          if (linktype == "spotLink") {
            $.ajax({
              type: "PUT",
              url: "https://api.spotify.com/v1/me/tracks",
              data: JSON.stringify({ ids: [linkid] }),
              headers: {
                Authorization: access_token,
                "Content-Type": "application/json",
              },
              success: function () {
                target.className = "heartLiked";
                target.innerText = "Saved!";
              },
              error: function () {
                alert("Unable to like song.");
              },
            });
          } else if (linktype == "spotAlbum") {
            $.ajax({
              type: "PUT",
              url: "https://api.spotify.com/v1/me/albums",
              data: JSON.stringify({ ids: [linkid] }),
              headers: {
                Authorization: access_token,
                "Content-Type": "application/json",
              },
              success: function () {
                target.className = "heartLiked";
                target.innerText = "Saved!";
              },
              error: function () {
                alert("Unable to like album.");
              },
            });
          } else if (linktype == "spotPlaylist") {
            $.ajax({
              type: "PUT",
              url: "https://api.spotify.com/v1/playlists/" + linkid + "/followers",
              headers: {
                Authorization: access_token,
                "Content-Type": "application/json",
              },
              success: function () {
                target.className = "heartLiked";
                target.innerText = "Saved!";
              },
              error: function () {
                alert("Unable to like playlist.");
              },
            });
          } else if (linktype == "spotArtist") {
            $.ajax({
              type: "PUT",
              url: "https://api.spotify.com/v1/me/following?type=artist&ids=" + linkid,
              headers: {
                Authorization: access_token,
                "Content-Type": "application/json",
              },
              success: function () {
                target.className = "heartLiked";
                target.innerText = "Saved!";
              },
              error: function () {
                alert("Unable to like artist.");
              },
            });
          }
        },
      });
    } else if (eventClass == "deleteComment" || eventClass == "deleteComment temp") {
      if (Confirm("comment")) {
        const commentid = $(target).data("commentid");
        $.post({
          url: "includes/removeGroupComment.php",
          data: { commentid: commentid },
          success: function () {
            target.remove();
            $("#comment" + commentid).fadeOut(1200);
          },
          error: function () {
            alert("Unable to delete comment.");
          },
        });
      }
    } else if (eventClass == "deletePost") {
      let postid = $(target).parent().parent().data("postid"),
        groupid = $(target).parent().parent().data("groupid");
      if (Confirm("post")) {
        $.post({
          url: "includes/removeGroupPost.php",
          data: { postid, groupid },
          success: function () {
            $("#post" + postid).slideUp(1200);
          },
          error: function () {
            alert("Unable to delete post.");
          },
        });
      }
    } else if (eventClass == "loadMoreCommButton") {
      const postId = $(target).data("postid"),
        numComments = parseInt($(target).attr("data-comments"));
      $.post({
        url: "includes/loadMoreGroupComments.php",
        data: { numComments, postId },
        success: function (data) {
          target.remove();
          $(".temp").remove();
          if ($("#commForm" + postId).length) {
            $(data).insertBefore($("#commForm" + postId));
          } else {
            $("#comments" + postId).append(data);
          }
          target.setAttribute("data-comments", numComments + 5);
        },
        error: function () {
          alert("Unable to load more comments.");
        },
      });
    } else if (eventClass == "loadMoreButton") {
      const groupid = $(target).data("groupid");
      $.post({
        url: "includes/loadMoreGroupPosts.php",
        data: { numPosts, groupid },
        success: function (data) {
          target.remove();
          $(".postAndComments").append(data);
          numPosts += 8;
        },
        error: function () {
          alert("Unable to load more posts.");
        },
      });
    }
  });
  groupFeed.addEventListener("keydown", (event) => {
    const target = event.target;
    if (event.key === "Enter" && target.className == "commentBox") {
      const postid = $(target).parent().parent().data("postid");
      const poster = $(target).parent().parent().data("poster");
      const title = $(target).parent().parent().data("title");
      const commentText = $("#commForm" + postid);
      $.post({
        url: "includes/newGroupComment.php",
        data: {
          text: commentText.val(),
          postid,
          poster,
          title,
        },
        success: function (data) {
          if ($("#loadMoreCommButton" + postid).length) {
            $(data)
              .hide()
              .insertBefore($("#loadMoreCommButton" + postid))
              .fadeIn(1500);
          } else {
            $(data)
              .hide()
              .insertBefore($("#commForm" + postid))
              .fadeIn(1500);
          }
          commentText.val("");
        },
        error: function () {
          if ($("#loadMoreCommButton" + postid).length) {
            $("<p>Comment failed to post.</p>").insertBefore($("#loadMoreCommButton" + postid));
          } else {
            $("<p>Comment failed to post.</p>").insertBefore($("#commForm" + postid));
          }
          commentText.val("");
        },
      });
    }
  });
  const members = document.getElementById("members"),
    memberButton = document.getElementById("memberButton"),
    postForm = document.getElementById("postForm"),
    postButton = document.getElementById("postButton");
  memberButton.onclick = function () {
    members.style.display = "block";
    postForm.style.display = "none";
    // postButton.style.backgroundColor = "#ffa31a";
    // memberButton.style.backgroundColor = "#3ca1c3";
    postButton.className = "notSelected hover";
    memberButton.className = "selected";
  };
  postButton.onclick = function () {
    postForm.style.display = "block";
    members.style.display = "none";
    // memberButton.style.backgroundColor = "#ffa31a";
    // postButton.style.backgroundColor = "#3ca1c3";
    memberButton.className = "notSelected hover";
    postButton.className = "selected";
  };
  const postSubmit = document.getElementById("postSubmitButton"),
    postInput = document.getElementById("postLink");
  if (postSubmit) {
    getBasicAuthFromHeroku()
      .then((authorization) => {
        auth = authorization;
        return getBasicTokenFromSpotify(authorization);
      })
      .then((token) => {
        basic_token = token; //sets the basic token for spotify searching
      });
    postSubmit.addEventListener("click", function () {
      const postLink = $("#postLink"),
        postText = $("#postText"),
        title = $("#postLink").attr("data-title"),
        groupid = $(postSubmit).data("groupid"),
        priv = $(postSubmit).data("private: priv");
      $.post({
        url: "includes/newGroupPost.php",
        data: { desc: postText.val(), link: postLink.val(), groupid, private: priv, title },
        success: function (data) {
          if (data.indexOf("ERROR -") != -1) {
            $("#postForm h6").remove();
            $("#postForm").prepend($(data));
          } else {
            $(data).hide().prependTo($("#groupFeed")).slideDown(3000);
            $("#noPosts").remove();
            numPosts++;
            postLink.val("");
            postText.val("");
            $(postInput).removeAttr("data-title");
          }
        },
        error: function () {
          alert("Error");
          postLink.val("");
          postText.val("");
        },
      });
    });
    searchRequest(postInput, getSearchResults, 1000);
    const searchUL = document.getElementById("searchResults");
    function getSearchResults() {
      const linkType = $("#linkType").val(),
        search = $(postInput).val();
      if (search == "") {
        //removes result section if no results (deleting)
        searchUL.style.display = "none";
        return;
      }
      $.get({
        url: "https://api.spotify.com/v1/search",
        headers: { Authorization: basic_token },
        data: { q: search, type: linkType, limit: "5" },
        success: function (results) {
          searchUL.textContent = "";
          if (linkType == "track") {
            if (results.tracks.items.length != 0) {
              results.tracks.items.forEach((result) => {
                let newResult = document.createElement("li"),
                  resultImage = document.createElement("img"),
                  resultInfo = document.createElement("span"),
                  resultName = document.createElement("p"),
                  resultArtist = document.createElement("span"),
                  src = document.createAttribute("src");
                src.value = result.album.images[2].url;
                resultImage.setAttributeNode(src);
                let url = document.createAttribute("data-exurl");
                url.value = result.external_urls.spotify;
                newResult.setAttributeNode(url);
                let title = document.createAttribute("data-title");
                title.value = result.name;
                newResult.setAttributeNode(title);
                newResult.classList.add("searchResult");
                resultName.innerText = result.name;
                resultArtist.innerText = result.artists[0].name;
                resultInfo.appendChild(resultName);
                resultInfo.appendChild(resultArtist);
                newResult.appendChild(resultImage);
                newResult.appendChild(resultInfo);
                searchUL.appendChild(newResult);
                searchUL.style.display = "block";
              });
            } else {
              searchUL.style.display = "none";
            }
          } else if (linkType == "album") {
            if (results.albums.items.length != 0) {
              results.albums.items.forEach((result) => {
                let newResult = document.createElement("li"),
                  resultImage = document.createElement("img"),
                  resultInfo = document.createElement("span"),
                  resultName = document.createElement("p"),
                  resultArtist = document.createElement("span"),
                  src = document.createAttribute("src");
                src.value = result.images[2].url;
                resultImage.setAttributeNode(src);
                let url = document.createAttribute("data-exurl");
                url.value = result.external_urls.spotify;
                newResult.setAttributeNode(url);
                let title = document.createAttribute("data-title");
                title.value = result.name;
                newResult.setAttributeNode(title);
                newResult.classList.add("searchResult");
                resultName.innerText = result.name;
                resultArtist.innerText = result.artists[0].name;
                resultInfo.appendChild(resultName);
                resultInfo.appendChild(resultArtist);
                newResult.appendChild(resultImage);
                newResult.appendChild(resultInfo);
                searchUL.appendChild(newResult);
                searchUL.style.display = "block";
              });
            } else {
              searchUL.style.display = "none";
            }
          } else if (linkType == "playlist") {
            if (results.playlists.items.length != 0) {
              results.playlists.items.forEach((result) => {
                let newResult = document.createElement("li"),
                  resultInfo = document.createElement("span"),
                  resultName = document.createElement("p"),
                  resultOwner = document.createElement("span"),
                  url = document.createAttribute("data-exurl");
                url.value = result.external_urls.spotify;
                newResult.setAttributeNode(url);
                let title = document.createAttribute("data-title");
                title.value = result.name;
                newResult.setAttributeNode(title);
                newResult.classList.add("searchResult");
                resultName.innerText = result.name;
                resultOwner.innerText = result.owner.display_name;
                resultInfo.appendChild(resultName);
                resultInfo.appendChild(resultOwner);
                newResult.appendChild(resultInfo);
                searchUL.appendChild(newResult);
                searchUL.style.display = "block";
              });
            } else {
              searchUL.style.display = "none";
            }
          } else if (linkType == "artist") {
            if (results.artists.items.length != 0) {
              results.artists.items.forEach((result) => {
                let newResult = document.createElement("li"),
                  resultImage = document.createElement("img"),
                  resultName = document.createElement("p"),
                  src = document.createAttribute("src");
                src.value = result.images[2].url;
                resultImage.setAttributeNode(src);
                let url = document.createAttribute("data-exurl");
                url.value = result.external_urls.spotify;
                newResult.setAttributeNode(url);
                let title = document.createAttribute("data-title");
                title.value = result.name;
                newResult.setAttributeNode(title);
                newResult.classList.add("searchResult");
                resultName.innerText = result.name;
                newResult.appendChild(resultImage);
                newResult.appendChild(resultName);
                searchUL.appendChild(newResult);
                searchUL.style.display = "block";
              });
            } else {
              searchUL.style.display = "none";
            }
          }
        },
        error: function () {
          alert("Spotify token expired. Please refresh the page.");
        },
      });
    }
    searchUL.addEventListener("click", function (event) {
      let target = event.target;
      if (target.className == "searchResult") {
        $(postInput).val(target.getAttribute("data-exurl"));
        $(postInput).attr("data-title", target.getAttribute("data-title"));
        searchUL.style.display = "none";
      }
    });
    const postType = document.getElementById("linkType");
    postType.addEventListener("click", function () {
      searchUL.style.display = "none";
    });
  }
  //left buttons for group options
  const leftButton = document.getElementById("leftButton");
  if (leftButton) {
    leftButton.addEventListener("click", function () {
      const buttonAction = $(leftButton).data("action"),
        groupid = $(leftButton).data("groupid");
      if (buttonAction == "join") {
        const host = $(leftButton).data("host"),
          title = $(leftButton).data("title");
        $.post({
          url: "includes/newGroupLike.php",
          data: { groupid, host, title },
          success: function (data) {
            if (data.indexOf("ERROR -") != -1) {
              $(".leftSidebar").append($(data));
            } else {
              $("#leftButton").remove();
              $("#leftButtons").prepend("<p>Group like successful.</p>");
            }
          },
          error: function () {
            alert("Like failed");
          },
        });
      } else if (buttonAction == "leave") {
        $.post({
          url: "includes/removeGroupLike.php",
          data: { groupid },
          success: function (data) {
            if (data.indexOf("ERROR-") != -1) {
              $("#leftButtons").prepend($(data));
            } else {
              $("#leftButton").remove();
              $("#leftButtons").prepend("<p>Group unlike successful.</p>");
            }
          },
          error: function () {
            alert("Leave group failed");
          },
        });
      } else if (buttonAction == "delete") {
        const confirm = Confirm("delete");
        if (confirm) {
          $.post({
            url: "includes/deleteGroup.php",
            data: { groupid },
            success: function (data) {
              if (data.indexOf("ERROR -") != -1) {
                $(".leftSidebar").append($(data));
              } else {
                window.location.href = "myGroups.php";
              }
            },
            error: function () {
              alert("Delete failed");
            },
          });
        }
      }
    });
  }
  const inviteButton = document.getElementById("inviteButton");
  if (inviteButton) {
    //Invite others to group, host only
    inviteButton.addEventListener("click", function () {
      const emails = $("#inviteArea").val(),
        message = $("#message").val(),
        groupid = $(inviteButton).data("groupid"),
        groupname = $(inviteButton).data("groupname");
      $.post({
        url: "includes/sendInvites.php",
        data: { groupid, groupname, emails, message },
        success: function (data) {
          if (data.indexOf("ERROR -") != -1) {
            $("#errorMessage").remove();
            $("#members").append(data.substr(7));
          } else {
            $("#invites").remove();
            $("#message").remove();
            $("#members").append('<p style="color: white;">Invites Sent.</p>');
          }
        },
        error: function () {
          $("#members").append('<p style="color: white;">Invites failed to send.</p>');
        },
      });
    });
  }
  function gatherSongsForPlaylist(groupId) {
    return new Promise((resolve, reject) => {
      $.post({
        //Creates array of songs
        url: "includes/createPlaylist.php",
        data: { groupId },
        success: function (songURIs) {
          resolve(songURIs.split(","));
        },
        error: function () {
          reject("Unable to add tracks to playlist.");
        },
      });
    });
  }
  const playlistButton = document.getElementById("playlistButton");
  if (playlistButton) {
    playlistButton.addEventListener("click", async function () {
      const groupId = $(playlistButton).data("groupid"),
        groupName = $(playlistButton).data("groupname"),
        playlistName = `Groupslync ${groupName} Playlist`,
        refresh_token = $.cookie("refresh_token");
      const access_token = await refreshSpotifyToken(refresh_token);
      const userId = await getUserIdFromSpotify(access_token);
      const [newPlaylistId, songArray] = await Promise.all([
        createSpotifyPlaylist(userId, playlistName, access_token),
        gatherSongsForPlaylist(groupId),
      ]);
      window.location.href = addSongsToPlaylist(newPlaylistId, songArray, access_token);
    });
  }
  const Confirm = (type) => {
    let x;
    if (type === "post") {
      x = confirm("Are you sure you want to delete this post? You cannot undo this action.");
    } else if (type === "comment") {
      x = confirm("Are you sure you want to delete this comment?");
    } else if (type === "delete") {
      x = confirm("Are you sure you want to delete this group? This action cannot be undone.");
    } else {
      return false;
    }
    return x;
  };
});
