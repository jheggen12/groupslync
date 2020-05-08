export function refreshSpotifyToken(refresh_token) {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: "https://musicauthbackend.herokuapp.com/refresh_token",
      data: { refresh_token },
      success: function (tokenInfo) {
        resolve(`Bearer ${tokenInfo.access_token}`);
      },
      error: function () {
        reject("Unable to reach Spotify. You may need to re-authorize.");
      },
    });
  });
}
export function getUserIdFromSpotify(access_token) {
  return new Promise((resolve, reject) => {
    $.get({
      url: "https://api.spotify.com/v1/me",
      headers: {
        Authorization: access_token,
        "Content-Type": "application/json",
      },
      success: function (userInfo) {
        resolve(userInfo.id);
      },
      error: function () {
        reject("Failed. You may need to re-authorize with Spotify.");
      },
    });
  });
}
export function createSpotifyPlaylist(userId, playlistName, access_token) {
  return new Promise((resolve, reject) => {
    $.post({
      //Creates Spotify playlist
      url: `https://api.spotify.com/v1/users/${userId}/playlists`,
      data: JSON.stringify({ name: playlistName }),
      headers: {
        Authorization: access_token,
        "Content-Type": "application/json",
      },
      success: function (newPlaylist) {
        resolve(newPlaylist.id);
      },
      error: function () {
        reject("Unable to create playlist. You may need to re-authorize with Spotify.");
      },
    });
  });
}
export function addSongsToPlaylist(newPlaylistId, songArray, access_token) {
  return new Promise((resolve, reject) => {
    $.post({
      url: `https://api.spotify.com/v1/playlists/${newPlaylistId}/tracks`,
      data: JSON.stringify({ uris: songArray }),
      headers: {
        Authorization: access_token,
        "Content-Type": "application/json",
      },
      success: function () {
        resolve(`playlist.php?id=${newPlaylistId}`);
      },
      error: function () {
        reject("Unable to add tracks to playlist.");
      },
    });
  });
}
export function searchRequest(searchBar, callback, delay) {
  let timer = null;
  searchBar.onkeyup = function () {
    if (timer) {
      window.clearTimeout(timer);
    }
    timer = window.setTimeout(function () {
      timer = null;
      callback();
    }, delay);
  };
}
export function getBasicAuthFromHeroku() {
  return new Promise((resolve) => {
    $.ajax({
      /*Basic spotify auth for search function*/
      url: "https://musicauthbackend.herokuapp.com/search",
      success: function (data) {
        resolve(`Basic ${data.token}`);
      },
    });
  });
}
export function getBasicTokenFromSpotify(auth) {
  return new Promise((resolve) => {
    $.post({
      url: "https://accounts.spotify.com/api/token",
      headers: { Authorization: auth },
      data: { grant_type: "client_credentials" },
      success: function (tokenInfo) {
        resolve(`Bearer ${tokenInfo.access_token}`);
      },
    });
  });
}
export function addTzoCookie() {
  let d = new Date();
  d.setTime(d.getTime() + 315360000000);
  const expires = "expires=" + d.toUTCString();
  let timezone_offset_seconds = new Date().getTimezoneOffset() * 60;
  timezone_offset_seconds = timezone_offset_seconds == 0 ? 0 : -timezone_offset_seconds;
  document.cookie = "tzo=" + (timezone_offset_seconds + 21600) + ";expires=" + expires;
  document.cookie = "cookies=yes;expires=" + expires;
  $("#cookies").fadeOut(500);
  window.location.reload();
}
