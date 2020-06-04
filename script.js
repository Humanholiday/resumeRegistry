function doValidate() {
  console.log("validating....");
  try {
    // addr = document.getElementById("email").value;
    addr = $("#email").val();
    // pword = document.getElementById("pword").value;
    pword = $("#pword").val();
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

countPos = 0;
//insert position fields upon click of Position:+ button. uses jquery to insert html inside a div
$(document).ready(function () {
  window.console && console.log("document ready called");
  $("#addPos").click(function (event) {
    event.preventDefault();
    if (countPos >= 9) {
      alert("Maximum of nine position entries exceeded");
      return;
    }
    countPos++;
    $("#position_fields").append(
      '<div id="position' +
        countPos +
        '"> \
        <p>Year: <input type="text" name="year' +
        countPos +
        '" value=""/> \
          <input type="button" value="-" \
           onclick="$(\'#position' +
        countPos +
        '\').remove();countPos--; return false;"></p> \
          <textarea name="desc' +
        countPos +
        '" rows="8" cols="80"></textarea>\
      </div>'
    );
  });
});

//FOR EDIT PAGE insert position fields upon click of Position:+ button. uses jquery to insert html inside a div. countEdit is the number of positions already submitted, drawn from the database.
$(document).ready(function () {
  window.console && console.log("document ready called");
  $("#editPos").click(function (event) {
    event.preventDefault();
    if (countEdit >= 9) {
      alert("Maximum of nine position entries exceeded");
      return;
    }
    countEdit++;
    $("#position_fields").append(
      '<div id="position' +
        countEdit +
        '"> \
        <p>Year: <input type="text" name="year' +
        countEdit +
        '" value=""/> \
          <input type="button" value="-" \
           onclick="$(\'#position' +
        countEdit +
        '\').remove();countEdit--; return false;"></p> \
          <textarea name="desc' +
        countEdit +
        '" rows="8" cols="80"></textarea>\
      </div>'
    );
  });
});
