function doValidate() {
  console.log("validating....");
  try {
    addr = document.getElementById("email").value;
    pword = document.getElementById("pword").value;
    console.log("validating addr= " + addr + "pword= " + pword);
    if (addr == null || addr == "" || pword == null || pword == "") {
      alert("Both fields must be filled out");
      return false;
    }
    if (addr.indexOf("@") == -1) {
      alert("Invalid email address");
      return false;
    }
    return true;
  } catch (e) {
    return false;
  }
  return false;
}
