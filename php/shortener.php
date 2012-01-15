<?php
include('config.php');
//print_r($_POST['full-url']);

function generateURL(&$pdo){
	$validURLchars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";

	try{
		$stmt = $pdo->query("SELECT count(*) FROM urls;");
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$count = $row['count(*)'];
		//echo 'row: ' . print_r($row, TRUE) . '<br>';
		$url = "";
		$baseLength = strlen($validURLchars);
		//echo 'count, base: ' . $count . ', ' . $baseLength . '<br>';
		do{
			$divisor = floor($count / $baseLength);
			//echo 'divisor: ' . $divisor . '<br>';
			if($divisor > 0){
				$url .= $validURLchars[$divisor - 1];
				$count = $count % $baseLength;
			}
			else{
				$url .= $validURLchars[$count % $baseLength];
				break;
			}
			
		}
		while($count > 0);
	}	
	catch(PDOException $e){
		print "<br><br>Error: " . print_r($e,TRUE);
		die();
	}
	
	return $url;
}

try{
	$pdo = new PDO('mysql:host=' . $SERVER  . ';dbname='. $DATABASE, $USER, $PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//TODO: use prepared statements
	$statement = $pdo->query("SELECT count(*) FROM urls WHERE full_url = '" . $_POST['full-url'] . "';");
	$row = $statement->fetch(PDO::FETCH_ASSOC);
//	print_r($row);
	echo 'length: ' . $row['count(*)'];
	if($row['count(*)'] > 0){
		$statement = $pdo->query("SELECT short_url FROM urls WHERE full_url ='" . $_POST['full-url']  . "';");
		$row = $statement->fetch(PDO::FETCH_ASSOC);
		print_r($row);
		echo 'short: ' . $row['short_url'];
	} 
	else{
		$statement->closeCursor();
		$short_url = generateURL($pdo);
		echo 'url: ' .$short_url;
		$query = "INSERT INTO urls (full_url, short_url) values('" . $_POST['full-url']  . "','" . $short_url  . "');";
		$pdo->query($query);
	}
	$pdo = null;
}
catch(PDOException $e){
	print "Error: " . $e.getMessage();
	die();
}

?>
