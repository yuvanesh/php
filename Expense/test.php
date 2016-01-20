<?php

$con=mysqli_connect("localhost","new","new");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$code = $_POST['code'];
	$button = $_POST['button'];
	
	$entityurl = 'http://localhost/expense/getentity.php';
	$fields = array(
		'code' => urlencode($code),
	);
	
	$fields_string = '';
	foreach($fields as $key=>$value) {
		$fields_string .= $key.'='.$value.'&';
	}
	
	rtrim($fields_string, '&');
	
	$ch = curl_init();

	curl_setopt($ch,CURLOPT_URL, $entityurl);
	curl_setopt($ch,CURLOPT_POST, count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	
	$result = json_decode($result,true);
		
	curl_close($ch);
	if( $result['msg'] == 'success' && $result['active'] == 'y' ) {
		echo "success $button";
	}
	else {
		echo "failure $button";
	}
}


$pagecontents = <<< EOPAGE

<html>
<head>
	<script type='text/javascript'>
		function submitaction(button) {
			var form = document.createElement("form");
			form.setAttribute("method", "post");
			form.setAttribute("action", "pypage.php");

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

<center>Enter Code</center>
<br>
<center>Code:&nbsp&nbsp<input type='text' name='code' id='code'></input></center>
<br>

<center>
<input type='submit' id='timesheet' name='button' value='TimeSheet' onclick='submitaction(this.value)' />
&nbsp&nbsp
<input type='submit' name='button' id='expense' value='Expense' onclick='submitaction(this.value)' />
</center>
<br>
<center><div id='errormessage'></div></center>

</body>
</html>

EOPAGE;

echo $pagecontents;
?>