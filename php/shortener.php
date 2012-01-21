<?php
include('config.php');
//print_r($_POST['full-url']);

$urlPrefix = 'http://www.ashanan.com/_'; // rewrite rules use '/_' to detect shortened urls

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
	
	$statement = $pdo->prepare("SELECT count(*) FROM urls WHERE full_url = ?");
	$statement->execute(array($_POST['full-url']));
	$row = $statement->fetch();
//	print_r($row);	
	if($row['count(*)'] > 0){
		$statement = $pdo->prepare("SELECT short_url FROM urls WHERE full_url = ?;");
		$statement->execute(array($_POST['full-url']));
		$row = $statement->fetch();
		
		$ret_url = $urlPrefix . $row['short_url'];
	} 
	else{
		$short_url = generateURL($pdo);
		$ret_url = $urlPrefix . $short_url;

		$statement->closeCursor();
		$statement = $pdo->prepare("INSERT INTO urls (full_url, short_url) values(:full_url, :short_url);");
		$statement->bindParam(':full_url', $_POST['full-url']);
		$statement->bindParam(':short_url', $short_url);
		$statement->execute();
	}
	$pdo = null;

	echo 'The shortened URL for "' . $_POST['full-url'] . '" is: <a href="' . $ret_url .'">' .  $ret_url . '</a>.';
}
catch(PDOException $e){
	print "Error: " . $e.getMessage();
	die();
}

?>
