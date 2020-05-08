import {
  refreshSpotifyToken,
  getBasicTokenFromSpotify,
  searchRequest,
  getBasicAuthFromHeroku,
  getUserIdFromSpotify,
  createSpotifyPlaylist,
  addSongsToPlaylist,
  addTzoCookie,
} from "./commonFunctions.js";

$(function () {
  const homeFeed = document.getElementById("homeFeed");
  let numPosts = 8,
    auth,
    basic_token;
  const cookieLink = document.getElementById("cookieLink");
  if (cookieLink) {
    cookieLink.addEventListener("click", addTzoCookie);
  }
  homeFeed.addEventListener("click", (event) => {
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
        url: "includes/likePublicPost.php",
        data: { postid: postid, poster: poster, title: title },
        success: function () {
          let likeCount = parseInt(spanElement.innerHTML);
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
        url: "includes/unlikePublicPost.php",
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
        //First get access_token using refresh token
        url: "https://musicauthbackend.herokuapp.com/refresh_token/",
        data: { refresh_token },
        success: function (tokenInfo) {
          const access_token = "Bearer " + tokenInfo.access_token;
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
                alert("Unable to like song. You may need to re-authenticate with Spotify.");
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
                alert("Unable to like album. You may need to re-authenticate with Spotify.");
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
                alert("Unable to like playlist. You may need to re-authenticate with Spotify.");
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
                alert("Unable to like artist. You may need to re-authenticate with Spotify.");
              },
            });
          }
        },
      });
    } else if (eventClass == "deleteComment" || eventClass == "deleteComment temp") {
      const commentid = $(target).data("commentid");
      if (Confirm("comment")) {
        $.post({
          url: "includes/removePublicComment.php",
          data: { commentid },
          success: function () {
            target.remove();
            $("#comment" + commentid).slideUp(1200);
          },
          error: function () {
            alert("Unable to delete comment.");
          },
        });
      }
    } else if (eventClass == "deletePost") {
      const postid = $(target).parent().parent().data("postid");
      if (Confirm("post")) {
        $.post({
          url: "includes/removePublicPost.php",
          data: { postid },
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
        url: "includes/loadMorePublicComments.php",
        data: { numComments, postId },
        success: function (data) {
          target.remove();
          $(".temp").remove();
          if ($("#commForm" + postId).length) $(data).insertBefore($("#commForm" + postId));
          else $("#comments" + postId).append(data);
          target.setAttribute("data-comments", numComments + 5);
        },
        error: function () {
          alert("Unable to load more comments.");
        },
      });
    } else if (eventClass == "loadMoreButton") {
      const genre = $(target).data("genre"),
        sort = $(target).data("sort");
      $.post({
        url: "includes/loadMorePublicPosts.php",
        data: { numPosts, genre, sort },
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
  homeFeed.addEventListener("keydown", (event) => {
    const target = event.target;
    if (event.key === "Enter" && target.className == "commentBox") {
      const postid = $(target).parent().parent().data("postid");
      const poster = $(target).parent().parent().data("poster");
      const title = $(target).parent().parent().data("title");
      const commentText = $("#commForm" + postid);
      $.post({
        url: "includes/newPublicComment.php",
        data: { text: commentText.val(), postid, poster, title },
        success: function (data) {
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
        error: function () {
          if ($("#loadMoreCommButton" + postid).length) $("<p>Comment failed to post.</p>").insertBefore($("#loadMoreCommButton" + postid));
          else $("<p>Comment failed to post.</p>").insertBefore($("#commForm" + postid));
          commentText.val("");
        },
      });
    }
  });
  const postSubmit = document.getElementById("postSubmitButton"),
    postInput = document.getElementById("postLink");
  if (postSubmit) {
    getBasicAuthFromHeroku()
      .then((authorization) => {
        auth = authorization;
        return getBasicTokenFromSpotify(authorization);
      })
      .then((token) => {
        basic_token = token;
      });
    postSubmit.addEventListener("click", function () {
      const postLink = $("#postLink"),
        postText = $("#postText"),
        genre = $("#linkGenre").val(),
        title = $("#postLink").attr("data-title");
      $.post({
        url: "includes/newPublicPost.php",
        data: {
          desc: postText.val(),
          link: postLink.val(),
          genre,
          title,
        },
        success: function (data) {
          if (data.indexOf("ERROR -") != -1) {
            $("#postForm h6").remove();
            $("#postForm").prepend($(data));
          } else {
            $(data).hide().prependTo($("#homeFeed")).slideDown(3000);
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
      let linkType = $("#linkType").val();
      let search = $(postInput).val();
      if (search == "") {
        //removes result section if no results
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
                newResult.classList.add(searchResult);
                resultName.innerHTML = result.name;
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
    searchUL.addEventListener("click", function () {
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
  function gatherSongsForPlaylist(genre, sort) {
    return new Promise((resolve, reject) => {
      $.post({
        //Creates array of songs
        url: "includes/createPublicPlaylist.php",
        data: { genre, sort },
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
      const genre = $(playlistButton).data("genre"),
        sort = $(playlistButton).data("sort"),
        refresh_token = $.cookie("refresh_token");
      let playlistName = "Groupslync ";
      if (genre) playlistName += genre;
      else playlistName = "Home Feed";
      if (sort) playlistName += " new";
      playlistName += " Playlist";
      const access_token = await refreshSpotifyToken(refresh_token);
      const userId = await getUserIdFromSpotify(access_token);
      const [newPlaylistId, songArray] = await Promise.all([
        createSpotifyPlaylist(userId, playlistName, access_token),
        gatherSongsForPlaylist(genre, sort),
      ]);
      window.location.href = addSongsToPlaylist(newPlaylistId, songArray, access_token);
    });
  }
  const Confirm = (type) => {
    let x;
    if (type === "post") x = confirm("Are you sure you want to delete this post? You cannot undo this action.");
    else if (type === "comment") x = confirm("Are you sure you want to delete this comment?");
    return x;
  };
});
