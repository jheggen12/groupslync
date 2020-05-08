$(function () {
  const main = document.getElementById("main");
  function gatherSongsForPlaylist(type, group, uid) {
    return new Promise((resolve, reject) => {
      $.post({
        //Creates array of songs
        url: "includes/createLikePlaylist.php",
        data: { type, group, uid },
        success: function (songURIs) {
          resolve(songURIs.split(","));
        },
        error: function () {
          reject("Unable to add tracks to playlist.");
        },
      });
    });
  }
  main.addEventListener("click", function (event) {
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
      let refresh_token = $.cookie("refresh_token"),
        access_token,
        newPlaylistId;
      refreshSpotifyToken(refresh_token)
        .then((token) => {
          access_token = token;
          return getUserIdFromSpotify(access_token);
        })
        .then((userId) => {
          return createSpotifyPlaylist(userId, playlistName, access_token);
        })
        .then((playlistId) => {
          newPlaylistId = playlistId;
          return gatherSongsForPlaylist(type, group, uid);
        })
        .then((songArray) => {
          return addSongsToPlaylist(newPlaylistId, songArray, access_token);
        })
        .then((windowUrl) => {
          window.location.href = windowUrl;
        });
    }
  });
});
