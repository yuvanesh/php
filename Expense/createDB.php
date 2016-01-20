<?php

$conn=mysqli_connect("localhost","new","new");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully"; 

$sql = "show DATABASES";

$result = mysqli_query($conn, $sql);
var_dump($result);
$result = mysqli_query($conn,"SHOW DATABASES");        
foreach( $result as $row ) {
	echo $row."<br>";
}
if (mysqli_query($conn, $sql)) {
    echo "Database successfully";
} else {
    echo "Error database: " . mysqli_error($conn);
}

?>