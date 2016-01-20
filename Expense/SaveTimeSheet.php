<?php

$con=mysqli_connect("localhost","new","new","testdbb");

if ($_SERVER['REQUEST_METHOD'] == 'GET' ) {
	echo "error: User code not valid<br>";
	echo "login <a href='http://localhost/expense/mypage.php'>here</a>";
	return;
}

$id = $date = $entityid = $start = $worked = $end = $comment = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	
	if (isset($_POST['button']) && $_POST['button'] == 'update') {
		$date = $_POST['date'];
		$entityid = $_POST['entityid'];
		$start = $_POST['start'];
		$worked = $_POST['worked'];
		$end = $_POST['End'];
		$comment = $_POST['comment'];
		
		if(isset($_POST['id']) and $_POST['id'] != '') {
			$id = $_POST['id'];
			$sql = "UPDATE `timesheet` set DATE = '$date', customer = '$worked', starttime = '$start', entityid = '$entityid', endtime= '$end', comment = '$comment', lastupdated = 'sysdate()' where id = '$id'";
			$id = $start = $worked = $end = $comment = '';
			unset($_POST['id']);
		} else {
			$sql = "INSERT INTO `timesheet` (`EntityID`, `Date`, `Customer`, `StartTime`, `EndTime`, `Comment`) VALUES ('$entityid','$date','$worked','$start','$end','$comment')";
			$id = $start = $worked = $end = $comment = '';
		}
		
		$con->query($sql);
	} else {
	
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
		$entityid = $result['entityid'];
		if( $result['msg'] == 'success' && $result['active'] == 'y' ) {
//			do nothing;
		}
		else {
			$ch = curl_init();
	
			curl_setopt($ch,CURLOPT_URL, 'http://localhost/expense/mypage.php');
			curl_setopt($ch,CURLOPT_POST, 1);
			curl_setopt($ch,CURLOPT_POSTFIELDS, "failure=".$result['msg']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
			$result = curl_exec($ch);
			curl_close($ch);
			echo $result;
			return;
		}
	}
}


$pagecontents = <<< EOPAGE

<html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.0/jquery.min.js"></script>
	<link rel="stylesheet" type="text/css" href="css4all.css">
	<script type='text/javascript'>
		
		function validateTime(time) {
			var regex = /([01]\d|2[0-3]):([0-5]\d):([0-5]\d)/;
			
			if(regex.test(time)) {
				return time;
			}
			else {
				alert('invalid time/format');
				return '';
			}
			
		}
		
		function clearfields() {
			document.getElementsByName('start')[0].value = '';
			document.getElementsByName('End')[0].value = '';
			document.getElementsByName('comment')[0].value = '';
			document.getElementsByName('worked')[0].value = '';
			document.getElementsByName('date')[0].value = '';
			document.getElementsByName('id')[0].value = '';
			document.getElementsByName('entityid')[0].value = '';
		}
		
		function UpdateRow(id) {
			jQuery.post("http://localhost/expense/gettimesheet.php",{id: id})
				.done(function( data ) {
					var obj = JSON.parse(data);
					console.log(obj);
					document.getElementsByName('start')[0].value = obj[0]['StartTime'];
					document.getElementsByName('End')[0].value = obj[0]['EndTime'];
					document.getElementsByName('comment')[0].value = obj[0]['Comment'];
					document.getElementsByName('worked')[0].value = obj[0]['Customer'];
					document.getElementsByName('date')[0].value = obj[0]['Date'];
					document.getElementsByName('id')[0].value = obj[0]['ID'];
					document.getElementsByName('entityid')[0].value = obj[0]['EntityID'];
				});
		}
		
		function DeleteRow(id) {
			jQuery.post("http://localhost/expense/gettimesheet.php",{id: id,delete: true})
				.done( function(data) {
					gettimesheet();
				});
				
		}
		
		function gettimesheet() {
			var date = document.getElementsByName('date')[0].value;
			if (date) {
				jQuery.post("http://localhost/expense/gettimesheet.php",{date: date})
					.done(function( data ) {
						var x = data;
						var obj = '';
						if(x) {
							obj = JSON.parse(x);
						}
						
						var cols = ["Date","EntityID","Customer","StartTime","EndTime","Comment"];
						var html = '';
						
						if(obj.length) {
							var html = "<table class='data'><tr>";
							html += "<th></th>";
							for	(index = 0; index < cols.length; index++) {
								html += "<th>" + cols[index] + "</th>";
							}
						
							for( i=0 ; i<obj.length; i++) {
								html += "<tr>";
								
								id = obj[i]['ID'];
								
								html += "<td class='hrefs'> <a href='#' onclick=UpdateRow("+ id +")>edit </a>&nbsp&nbsp&nbsp<a href='#' onclick=DeleteRow("+ id +")>delete</a> </td>";
								for	(index = 0; index < cols.length; index++) {
									html += "<td>" + obj[i][cols[index]] + "</td>";
								}
								html += "</tr>";
							}
							html += "</table>";
						}
						
						var print = document.getElementById('print');
						print.innerHTML = html;
					});
			}
		}
		jQuery( document ).ready(function() {
			gettimesheet();	
		});
	</script>
<title>
	TimeSheet
</title>
</head>

<body>

<form name="Timesheet" method='post' action='savetimesheet.php'>
	<input type='hidden' name='entityid' value=$entityid></input>
	<input type='hidden' name='id' value=$id></input>
	<center><h3>Timesheet Entry</h3></center>
	<center><table class='fields'>
	<tr></tr><tr></tr><tr></tr><tr></tr>
	<tr>
		<td class="labels">	Date	</td>
		<td>	<input type="date" name="date" value=$date>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type='button' class='myButton' value='Find' onclick='gettimesheet()'/>	</td>
	</tr>
	<tr>
		<td	class="labels">	Worked On</label>	</td>
		<td>
			<select name='worked'>
				<option value=""></option>
				<option value="Tiff">Tiff</option>
				<option value="MWC">MWC</option>
				<option value="Suede">Suede</option>
				<option value="Other">Other</option>
			</select>&nbsp
			<input placeholder='Start (hh:mm:ss)' type='text' size='10' name='start' onblur="this.value = validateTime(this.value)" value=$start></input>
			&nbsp-&nbsp&nbsp
			<input placeholder='End (hh:mm:ss)' type='text' size=10  onblur="this.value = validateTime(this.value)" name='End' value=$end></input>	
		</td>
	</tr>
	<tr>
		<td class='labels'>	Comment</label>	</td>
		<td>	<textarea name='comment' rows="4" cols="45" value=$comment></textarea>	</td>
	</tr>
	</table></center>

<center><input type='submit' name='button' value='update'></input>&nbsp&nbsp&nbsp<input type='button' name='clear' value='clear' onclick='clearfields();'></input></center><br><br>

<div id='print' class='print'></div>

</body>
</html>

EOPAGE;

echo $pagecontents;
?>