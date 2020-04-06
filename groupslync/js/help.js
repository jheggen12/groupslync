const ConfirmDelete = () => {
  return confirm("Are you sure you want to delete your account? There is no way to recover the content.");
};
$(function() {
  const options = document.getElementById("options"),
    passwordSection = document.getElementById("passwordSection"),
    emailSection = document.getElementById("emailSection"),
    deleteSection = document.getElementById("deleteSection"),
    messageSection = document.getElementById("message");
  options.addEventListener("click", event => {
    const eventClass = event.target.className;
    if (eventClass == "password") {
      messageSection.style.display = "none";
      passwordSection.style.display = "block";
      emailSection.style.display = "none";
      deleteSection.style.display = "none";
    } else if (eventClass == "email") {
      messageSection.style.display = "none";
      emailSection.style.display = "block";
      passwordSection.style.display = "none";
      deleteSection.style.display = "none";
    } else if (eventClass == "delete") {
      messageSection.style.display = "none";
      deleteSection.style.display = "block";
      emailSection.style.display = "none";
      passwordSection.style.display = "none";
    }
  });
  const deleteButton = document.getElementById("deleteAccount");
  deleteButton.addEventListener("click", function() {
    if (ConfirmDelete("delete")) {
      $.post({
        url: "includes/deleteAccount.php",
        data: { delete: "yes" }
      });
    }
  });
  const emailButton = document.getElementById("emailDev");
  emailButton.addEventListener("click", function() {
    const message = $("#messageContent").val();
    $.post({
      url: "includes/emailDev.php",
      data: { message },
      success: function(data) {
        messageSection.innerHTML = data;
        messageSection.style.display = "block";
        emailSection.style.display = "none";
      },
      error: function() {
        messageSection.innerHTML = "Email failed to send. Try again.";
        messageSection.style.display = "block";
      }
    });
  });
});
