$(function() {
  const findGroups = document.getElementById("findGroups");
  findGroups.addEventListener("click", event => {
    const target = event.target,
      eventClass = event.target.className;
    if (eventClass == "far fa-star") {
      const groupid = $(target).data("groupid"),
        host = $(target).data("host"),
        title = $(target).data("title");
      $.post({
        url: "includes/newGroupLike.php",
        data: { groupid, host, title },
        success: function() {
          target.style.color = "gold";
          target.className = "far fa-star liked";
          $(target)
            .parent()
            .parent()
            .fadeToggle(1000, removeGroup);
        }
      });
    }
  });
  const removeGroup = () => {
    //If last result on page, hide table
    $(target)
      .parent()
      .parent()
      .remove();
    if ($("#table > tbody > tr").length == 1) {
      $("#table").hide();
    }
  };
});
