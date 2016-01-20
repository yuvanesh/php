<?php

$conn=mysqli_connect("localhost","new","new","testdbb");

$sql = "create table user(id INT(6), password VARCHAR(20) , entityid VARCHAR(20) , active VARCHAR(1))";
if (mysqli_query($conn, $sql) === TRUE) {
	echo "Table created successfully";
	$sql = "insert into user values(1,'qwerty','xxxx','y')";
	mysqli_query($conn, $sql);
	$sql = "insert into user values(1,'asdfg','xxxx','n')";
	mysqli_query($conn, $sql);
} else {
	echo "Failed";
}
?>