$(function() {
  const clearNotifButton = document.getElementById("clearNotif");
  if (clearNotifButton) {
    clearNotifButton.addEventListener("click", function() {
      $.post({
        url: "includes/clearNotif.php",
        success: function(data) {
          if (data.indexOf("ERROR -") != -1) {
            $("#notifications").fadeOut(500);
          } else {
            $("#notifications").fadeOut(500);
            $(".fa-bell").fadeOut(500);
          }
        },
        error: function() {
          alert("Unable to remove notifications");
        }
      });
    });
  }
});
