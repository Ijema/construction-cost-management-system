<!DOCTYPE html>
<html>
<body>

<p>A function is triggered when the user releases a key in the input field. The function outputs the actual key/letter that was released inside the text field.</p>

Enter your name: <input type="text" id="fname" onkeyup="myFunction()">

<p>My name is: <span id="demo"></span></p>

<script>
function myFunction() {
  var x = document.getElementById("fname").value;
  document.getElementById("demo").innerHTML = x;
}
</script>

</body>
</html>
