<?php
include('config.php');
//print_r($_POST['full-url']);

function generateURL(){
		
}

try{
	$pdo = new PDO('mysql:host=' . $SERVER  . ';dbname='. $DATABASE, $USER, $PASSWORD);
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
		$short_url = generateURL();
		$query = "INSERT INTO urls (full_url, short_url) values('" . $_POST['full-url']  . "','" . $short_url  . "');";
	}
	$pdo = null;
}
catch(PDOException $e){
	print "Error: " . $e.getMessage();
	die();
}

?>
