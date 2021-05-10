<?php
	session_start();

	include_once("support/connection.php");		
	include_once('support/admin.php');
	
	$optionalAdmin = Admin::panel();

?><!DOCTYPE html>
<html>
<head>
	<title><?php echo $config['title']; ?></title>

	<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="support/style.css">
	<script type="text/javascript" src="support/jquery.min.js"></script>
	
	<meta charSet="utf-8"/>
	<meta http-equiv="x-ua-compatible" content="ie=edge"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

</head>
<body>

	<nav id="navigationbar" class="navbar navbar-toggleable-md navbar-light bg-faded">
		  <span class="navbar-brand"><?php echo $config['headline']; ?></span>
		  <a href='<?php echo $config['contributionURL']; ?>' class="btn btn-success navRight">Contribute</a>
	</nav>

	<div class="story" id="ingress">
		<div><?php echo $config['ingress']; ?></div>
	</div>

	<?php
	
	print $optionalAdmin;
	
	include_once('support/messages.php');
	showMessages();
	
	?>
</body>
</html>
