<?php

$con=mysqli_connect("localhost","new","new","testdbb");

if ($_SERVER['REQUEST_METHOD'] == 'GET' ) {
	echo "error: User code not valid<br>";
	echo "login <a href='http://localhost/expense/mypage.php'>here</a>";
	return;
}

$id = $entityid = $date = $amount = $taxamount = $transaction = $payment = $comment = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	if (isset($_POST['button']) && $_POST['button'] == 'update') {
		$date = $_POST['date'];
		$entityid = $_POST['entityid'];
		$comment = $_POST['comment'];
		$amount = $_POST['amount'];
		$taxamount = $_POST['tax'];
		$transaction = $_POST['transaction'];
		$payment = $_POST['payment'];
		
		if( $payment == 'Credit Card') {
			$payment = $_POST['cardtype'];
		}
		else if ($payment == 'Internet') {
			$payment = $_POST['internet'];
		}
		
		if(isset($_POST['id']) and $_POST['id'] != '') {
			$id = $_POST['id'];
			$sql = "UPDATE `expense` set DATE = '$date', entityid = '$entityid', comment = '$comment',amt = '$amount', taxamt = '$taxamount', type = '$transaction', payby = '$payment', lastupdated = 'sysdate()' where id = '$id'";
			$id = $amount = $taxamount = $transaction = $payment = $comment = '';
			unset($_POST['id']);
		} else {
			$sql = "INSERT INTO `expense` (`EntityID`, `Date`, `Amt`, `TaxAmt`, `Type`, `Comment`, `PayBy`) VALUES ('$entityid','$date','$amount','$taxamount','$transaction','$comment','$payment')";
			$id = $amount = $taxamount = $transaction = $payment = $comment = '';
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
//			echo "success $button";
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
	
		function clearfields() {
			document.getElementsByName('amount')[0].value = '';
					document.getElementsByName('tax')[0].value = '';
					document.getElementsByName('comment')[0].value = '';
					document.getElementsByName('transaction')[0].value = '';
					document.getElementsByName('payment')[0].value = '';
					document.getElementsByName('date')[0].value = '';
					document.getElementsByName('id')[0].value = '';
					document.getElementsByName('entityid')[0].value = '';
		}
	
		function validateamount(amt) {
			var numnum = +amt;
			if( ! isNaN(numnum) ) {
				return amt;
			}
			else {
				alert('Not an amount');
				return '';
			}
		}
	
		function property(value) {
			if (value == "Credit Card") {
				document.getElementsByName('cardtype')[0].style.display = "inline-block";
				document.getElementsByName('internet')[0].style.display = "none";
			} 
			else if (value == "Internet") {
				document.getElementsByName('cardtype')[0].style.display = "none";
				document.getElementsByName('internet')[0].style.display = "inline-block";
			}
			else {
				document.getElementsByName('cardtype')[0].style.display = "none";
				document.getElementsByName('internet')[0].style.display = "none";
			}
		}
		
		function DeleteRow(id) {
			jQuery.post("http://localhost/expense/getexpense.php",{id: id,delete: true})
				.done( function(data) {
					getexpense();
				});
				
		}
		
		function UpdateRow(id) {
			jQuery.post("http://localhost/expense/getexpense.php",{id: id})
				.done(function( data ) {
					var obj = JSON.parse(data);
					document.getElementsByName('amount')[0].value = obj[0]['Amt'];
					document.getElementsByName('tax')[0].value = obj[0]['TaxAmt'];
					document.getElementsByName('comment')[0].value = obj[0]['Comment'];
					document.getElementsByName('transaction')[0].value = obj[0]['Type'];
					document.getElementsByName('payment')[0].value = obj[0]['Payby'];
					document.getElementsByName('date')[0].value = obj[0]['Date'];
					document.getElementsByName('id')[0].value = obj[0]['ID'];
					document.getElementsByName('entityid')[0].value = obj[0]['EntityID'];
					document.getElementsByName('cardtype')[0].value = '';
					document.getElementsByName('internet')[0].value = '';
					
					var cardarray = ['CIBC','COSTCO','AMEX','SCOTIACARD','RBCCARD'];
					var netarray = ['SCOTIA','RBC','SCOTIA2'];
										
					if(jQuery.inArray(obj[0]['Payby'],cardarray) != -1) {
						document.getElementsByName('cardtype')[0].style.display = "inline-block";
						document.getElementsByName('internet')[0].style.display = "none";
						document.getElementsByName('payment')[0].value = 'Credit Card';
						document.getElementsByName('cardtype')[0].value = obj[0]['Payby'];
					}
					else if(jQuery.inArray(obj[0]['Payby'],netarray) != -1) {
						document.getElementsByName('cardtype')[0].style.display = "none";
						document.getElementsByName('internet')[0].style.display = "inline-block";
						document.getElementsByName('payment')[0].value = 'Internet';
						document.getElementsByName('internet')[0].value = obj[0]['Payby'];
					}
					else {
						document.getElementsByName('cardtype')[0].style.display = "none";
						document.getElementsByName('internet')[0].style.display = "none";
						document.getElementsByName('payment')[0].value = 'Cash';
					}
				});
		}
	
		function getexpense() {
			var date = document.getElementsByName('date')[0].value;
			if (date) {
				jQuery.post("http://localhost/expense/getexpense.php",{date: date})
					.done(function( data ) {
						var x = data;
						var obj = JSON.parse(x);
						
						var cols = ["Date","EntityID","Amt","TaxAmt","Type","Payby","Comment"];
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
			getexpense();
		});
	</script>
	<title>
		Expense
	</title>
</head>
<body>
<form name="Expense" method='post' action='saveexpense.php'>
	<input type='hidden' name='entityid' value=$entityid></input>
	<input type='hidden' name='id' value=$id></input>
	<center><h3>Expense Entry</h3></center>
	<center>
	<table class='fields'>
		<tr></tr><tr></tr><tr></tr><tr></tr>
		<tr>
			<td>	Date	</td>
			<td>	<input type="date" name="date" value=$date>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type='button' value='Find' onclick='getexpense()'/>	</td>
		</tr>
		<tr>
			<td>	Amount	</td>
			<td>	<input type='text' name='amount' onblur="this.value = validateamount(this.value)" value=$amount></input>
		</tr>
		<tr>
			<td>	Tax	</td>
			<td>	<input type='text' name='tax' onblur="this.value = validateamount(this.value)" value=$taxamount></input>	</td>
		</tr>
		<tr>
			<td>	Transaction type	</td>
			<td>
				<select name='transaction'>
					<option value=""></option>
					<option value="Gas">Gas</option>
					<option value="Rest">Rest</option>
					<option value="Business">Business</option>
					<option value="Entertainment">Entertainment</option>
					<option value="Rental">Rental</option>
					<option value="Other">Other</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>	Paid By	</td>
			<td>
				<select name='payment' onchange='property(this.value);'>
					<option value=""></option>
					<option value="Cash">Cash</option>
					<option value="Credit Card">Credit Card</option>
					<option value="Internet">Internet</option>
				</select>
				<select name='cardtype' style="display:none;">
					<option value=""></option>
					<option value="CIBC">CIBC</option>
					<option value="COSTCO">COSTCO</option>
					<option value="AMEX">AMEX</option>
					<option value="SCOTIACARD">SCOTIA</option>
					<option value="RBCCARD">RBC</option>
				</select>
				<select name='internet' style="display:none;">
					<option value=""></option>
					<option value="SCOTIA">SCOTIA</option>
					<option value="RBC">RBC</option>
					<option value="SCOTIA2">SCOTIA2</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>	Comment	</td>
			<td>	<textarea name='comment' rows="4" cols="30" value=$comment></textarea>	</td>
		</tr>
	</table>
	</center>
	<center><input type='submit' name='button' value='update'>&nbsp&nbsp&nbsp</input><input type='button' name='clear' value='clear' onclick='clearfields();'></input></center>
</form>

<div id='print' class='print'></div>

</body>
</html>

EOPAGE;

echo $pagecontents;
?>