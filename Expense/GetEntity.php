<?php

$con=mysqli_connect("localhost","new","new","testdbb");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$code = $_POST['code'];
	
	$form_fields = array(
		'code' => $code
	);
	
	$sql = "SELECT password,entityid,active FROM user where password = '$code'";
	
	$result = $con->query($sql);
	
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$result = array(
						"active" => $row["active"],
						"entityid" => $row["entityid"],
						"msg" => ($row["active"] == 'y') ? "success" : "error: User Inactive",
		);
	} else if ($result->num_rows > 0) {
		$result = array(
						"entityid" => '',
						"msg" => "Unknown error",
						"active" => '',
		);
	} else {
		$result = array(
						"entityid" => '',
						"active" => '',
						"msg" => "error: User Not Available"
		);
	}
	
	
	header('Content-type: application/json');
	echo json_encode($result);
	
}


?>