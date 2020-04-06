$(function() {
  const deleteButton = document.getElementById("deleteButton");
  if (deleteButton) {
    const playlistId = $(deleteButton).data("playlistid");
    deleteButton.addEventListener("click", function() {
      const refresh_token = $.cookie("refresh_token");
      $.ajax({
        //First refresh the spotify token
        url: "https://musicauthbackend.herokuapp.com/refresh_token",
        data: { refresh_token },
        success: function(tokenInfo) {
          const access_token = "Bearer " + tokenInfo.access_token;
          $.ajax({
            //Unfollow Spotify playlist
            type: "DELETE",
            url: `https://api.spotify.com/v1/playlists/${playlistId}/followers`,
            headers: { Authorization: access_token },
            success: function() {
              window.location.href = "index.php";
            },
            error: function() {
              window.location.href = "index.php";
            }
          });
        }
      });
    });
  }
});
