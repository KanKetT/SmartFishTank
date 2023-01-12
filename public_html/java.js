function check() {
  if (
    document.getElementById("name").value == "iamreal" &&
    document.getElementById("pass").value == "rai"
  ) {
    location.href = "main.php";
  } else {
    alert("validation failed");
    location.href = "connect.html";
  }
}
