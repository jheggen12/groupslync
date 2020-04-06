const ConfirmDelete = type => {
  let x;
  if (type === "delete") {
    x = confirm("Are you sure you want to delete this group? There is no way to recover the content.");
  } else if (type === "unlike") {
    x = confirm("Are you sure you want to unlike this group?");
  }
  return x;
};
$(function() {
  const myGroups = document.getElementById("myGroups");
  myGroups.addEventListener("click", event => {
    const target = event.target,
      eventClass = event.target.className;
    if (eventClass == "groupRow") {
      const groupid = $(target).data("groupid");
      window.location.href = "group.php?id=" + groupid;
    } else if (eventClass == "fas fa-trash-alt") {
      //Delete group
      const groupid = $(target).data("groupid");
      if (ConfirmDelete("delete")) {
        $.post({
          url: "includes/deleteGroup.php",
          data: { groupid },
          success: function() {
            $(target)
              .parent()
              .fadeOut(2000);
          }
        });
      }
    } else if (eventClass == "fas fa-times") {
      //Remove like
      const groupid = $(target).data("groupid");
      if (ConfirmDelete("unlike")) {
        $.post({
          url: "includes/removeGroupLike.php",
          data: { groupid },
          success: function(data) {
            if (data.indexOf("ERROR-") != -1) {
              alert(data);
            } else {
              $(target)
                .parent()
                .fadeOut(2000);
            }
          }
        });
      }
    }
  });
});
