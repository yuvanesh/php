<?php


$con=mysqli_connect("localhost","new","new","testdbb");

//if ($_SERVER['REQUEST_METHOD'] == 'GET' ) {
//	echo "error: User code not valid<br>";
//	echo "login <a href='http://localhost/expense/mypage.php'>here</a>";
//	return;
//}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$sql = '';
	if(isset($_POST['id'])) {
		$id = $_POST['id'];
		if(isset($_POST['delete'])) {
			$sql = "delete FROM expense where id = '$id'";
		}
		else {
			$sql = "SELECT * FROM expense where id = '$id'";
		}
	} else {
		$date = $_POST['date'];	
		$sql = "SELECT * FROM expense where date = '$date'";
	}
	$result = $con->query($sql);
	$jsonencoded = '';
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			if(!$jsonencoded) {
				$jsonencoded .= '[' . json_encode($row);
			} else {
				$jsonencoded .= ',' . json_encode($row);
			}
		}
	}
	$jsonencoded .= ']';
	echo $jsonencoded;
}
?>