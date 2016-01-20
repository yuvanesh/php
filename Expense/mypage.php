<?php

$con=mysqli_connect("localhost","new","new");

$failed = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$failed = $_POST['failure'];
}


$pagecontents = <<< EOPAGE

<html>
<head>
	<link rel="stylesheet" type="text/css" href="css4all.css">
	<script type='text/javascript'>
		function submitaction(button) {
			var form = document.createElement("form");
			form.setAttribute("method", "post");
			if(button == 'TimeSheet') {
				form.setAttribute("action", "SaveTimeSheet.php");
			} else {
				form.setAttribute("action", "SaveExpense.php");
			}

			var code = document.getElementById('code').value;
			
			var field1 = document.createElement("input");
			field1.setAttribute("name","code");	
			field1.setAttribute("value", code);
			field1.setAttribute("type", "hidden");
			
			var field2 = document.createElement("input");
			field2.setAttribute("name","button");	
			field2.setAttribute("value", button);
			field2.setAttribute("type", "hidden");
			
			form.appendChild(field1);
			form.appendChild(field2);
			
			form.submit();
		}
	</script>
<title>
	Login & Menu selection Screen
</title>
</head>
<body>

<!-- <center><h3>Enter Code<h3></center> -->
<center><input placeholder='code' type='text' name='code' id='code'></input></center>
<br>

<center>
<input type='submit' id='timesheet' name='button' value='TimeSheet' onclick='submitaction(this.value)' />
&nbsp&nbsp
<input type='submit' name='button' id='expense' value='Expense' onclick='submitaction(this.value)' />
</center>
<br>
<center><div id='errormessage'>$failed</div></center>

</body>
</html>

EOPAGE;

echo $pagecontents;
?>