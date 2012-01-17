<?php

include('config.php');

try{
	$pdo = new PDO('mysql:host=' . $SERVER  . ';dbname='. $DATABASE, $USER, $PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	//TODO: use prepared statements
	$statement = $pdo->query("SELECT full_url FROM urls WHERE short_url = '" . $_GET['id'] . "';");

	$row = $statement->fetch(PDO::FETCH_ASSOC);
	if($row){
		print_r($row);
	}
	else{
		echo "404";
	}
}
catch(PDOException $e){
	print "Error: " . $e.getMessage();
	die();
}
echo $_GET['id'];

?>
