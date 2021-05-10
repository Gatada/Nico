<?php

// Remote Location
@include_once('../../../nico/support/messages.php');
@include_once('../../../nico/support/connection.php');

// Local location
@include_once('../../Reading/support/messages.php');
@include_once("../../Reading/support/connection.php");

session_start();


if(array_key_exists('content', $_POST)){
	
	$content = mysqli_real_escape_string($link, $_POST['content']);
	$userID = mysqli_real_escape_string($link,$_SESSION['id']);

	$query = "UPDATE `users` SET `message` = \"$content\", `created` = NOW() WHERE id = '$userID' LIMIT 1";
	if (mysqli_query($link,$query)) {
		echo previewMessage();		
	}else{
		echo "fail";
	}
}

?>