<?php

include('config.php');

try{
	$pdo = new PDO('mysql:host=' . $SERVER  . ';dbname='. $DATABASE, $USER, $PASSWORD);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	$statement = $pdo->prepare("SELECT full_url FROM urls WHERE short_url = ?");
	$statement->execute(array($_GET['id']));

	$row = $statement->fetch();
	if($row){
		header('Location: ' .  $row['full_url']);
	}
	else{
		echo "404";
	}
}
catch(PDOException $e){
	print "Error: " . $e.getMessage();
	die();
}

?>
