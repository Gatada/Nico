<?php

	// Remote Location
	@include_once('../../../nico/support/connection.php');
	@include_once('../../../nico/support/messages.php');
	
	// Local location
	@include_once('../../Reading/support/connection.php');
	@include_once('../../Reading/support/messages.php');

	session_start();

	if(array_key_exists('content', $_POST)) {
				
		$unpublish = ($_GET['unpublish'] == 'true');
		$userID = mysqli_real_escape_string($link, $_SESSION['id']);

		if ($unpublish) {
			$query = "UPDATE `users` SET `isPublished` = 0 WHERE id = '$userID' LIMIT 1" ;			
		} else {
			$message = mysqli_real_escape_string($link,$_POST['content']);
			$query = "UPDATE `users` SET `message` = '$message', `isPublished` = 1, `created` = NOW() WHERE id = '$userID' LIMIT 1";
		}

		$result = mysqli_query($link, $query);
		
		if($result){
			echo previewMessage();
			
		} else {
			echo "fail";
		}
	}

?>