$(function() {
  const main = document.getElementById("main");
  main.addEventListener("click", function(event) {
    const target = event.target,
      eventClass = event.target.className;
    if (eventClass == "playlistButton") {
      const type = $(target).data("type"),
        group = $(target).data("group"),
        uid = $(target).data("uid"),
        refresh_token = $.cookie("refresh_token");
      let playlistName;
      if (group == 1) playlistName = `Groupslync ${uid} group ${type}`;
      else playlistName = `Groupslync ${uid} public ${type}`;
      $.ajax({
        //Adds the songs to the Spotify playlist
        url: "https://musicauthbackend.herokuapp.com/refresh_token",
        data: { refresh_token },
        success: function(tokenInfo) {
          const access_token = "Bearer " + tokenInfo.access_token;
          $.get({
            //Gets user ID from spotify
            url: "https://api.spotify.com/v1/me",
            headers: {
              Authorization: access_token,
              "Content-Type": "application/json"
            },
            success: function(userInfo) {
              let userId = userInfo.id;
              $.post({
                //Creates Spotify playlist
                url: `https://api.spotify.com/v1/users/${userId}/playlists`,
                data: JSON.stringify({ name: playlistName }),
                headers: {
                  Authorization: access_token,
                  "Content-Type": "application/json"
                },
                success: function(newPlaylistInfo) {
                  const newPlaylistId = newPlaylistInfo.id;
                  $.post({
                    //Creates array of songs
                    url: "includes/createLikePlaylist.php",
                    data: { type, group, uid },
                    success: function(songURIs) {
                      let songArray = songURIs.split(",");
                      $.post({
                        //Adds the songs to the Spotify playlist
                        url: `https://api.spotify.com/v1/playlists/${newPlaylistId}/tracks`,
                        data: JSON.stringify({ uris: songArray }),
                        headers: {
                          Authorization: access_token,
                          "Content-Type": "application/json"
                        },
                        success: function() {
                          window.location.href = "playlist.php?id=" + newPlaylistId;
                        },
                        error: function() {
                          alert("Unable to add tracks to playlist.");
                        }
                      });
                    },
                    error: function() {
                      alert("Unable to add tracks to playlist.");
                    }
                  });
                },
                error: function() {
                  alert("Unable to create playlist. You may need to re-authorize with Spotify.");
                }
              });
            },
            error: function() {
              alert("Failed. You may need to re-authorize with Spotify.");
            }
          });
        },
        error: function() {
          alert("Unable to reach Spotify. You may need to re-authorize.");
          return "";
        }
      });
    }
  });
});
