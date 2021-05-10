<?php

// Remote Location
@include_once('../../nico/support/config.php');
@include_once('../../nico/support/connection.php');

// Local location
@include_once('../Reading/support/config.php'); 
@include_once('../Reading/support/connection.php');

session_start();

$error = "";
$success = "";
$show_login = false;

if(array_key_exists("logout", $_GET)){
	unset($_SESSION);
	session_destroy();
	setcookie('id', '', time() - 60*60*24*356, 'basberg.com' );
	$_COOKIE['id'] = '';
	
	header('Location: '. $config['readingURL']);

}else if((array_key_exists("id", $_SESSION)  AND $_SESSION['id']) OR (array_key_exists("id", $_COOKIE) AND $_COOKIE['id'] )){
	header("Location: contribute.php");

}

if(array_key_exists('email', $_POST) OR array_key_exists('password', $_POST)){
	$show_login = ($_POST['sign'] == '0');
	
	if (empty($_POST['email']) && empty($_POST['password'])) {
		$error = "<p>Please enter Email and Password.</p>";
		
	} elseif ($_POST['email'] == '') {
		$error = "<p>Enter Your Email</p>";

	}else if($_POST['password'] == ''){
		$error = "<p>Enter Your Password</p>";

	} else {

		if($_POST['sign'] == '1'){

			//######## php code for For sign up part  ###############
			
			if (strcmp($_POST['Security'], $config['secretWord']) !== 0) {
				$error = "<p>Sorry, wrong security answer.</p>";
				
			} elseif ($_POST['password'] != $_POST['ConfirmPassword']){

				$error = "<p>Password does't match</p>";
			} else {

				$query = "SELECT `id` FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'LIMIT 1";

				$result = mysqli_query($link, $query);


				if(mysqli_num_rows($result) > 0){
					$error = "<p>Email already registered..</p>";

				}else{

					$query = "INSERT INTO `users`(`email`, `password`) VALUES ('".mysqli_escape_string($link, $_POST['email'])."','".mysqli_escape_string($link, $_POST['password'])."')";

					$result = mysqli_query($link, $query);

					if($result) {
				
						$query = "UPDATE `users` SET password = '". md5(md5(mysqli_insert_id($link)).$_POST['password'])."' WHERE id = '".mysqli_insert_id($link)."'LIMIT 1";

						if(mysqli_query($link, $query)){

							$success = "<p>Sign up successful! Please log in to your account.</p>";
							$show_login = true;
						}

						$_SESSION['id'] = mysqli_insert_id($link);

					}else{

						 $error = "<p>Failed to sign you up. Please try again..";
						 
						 print('<pre>');
						 var_dump($link);
						 var_dumpt($result);
						 print('</pre>');
					}
				}
			}		

		}else if($_POST['sign'] == '0'){

				//######## php code for For login part  ###############

			$query = "SELECT * FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."' LIMIT 1";

			$result = mysqli_query($link, $query); 
			$row = mysqli_fetch_array($result);

			if(isset($row)) {

				$codedPassword = md5(md5($row['id']).$_POST['password']);

				if(strcmp($codedPassword, $row['password']) === 0) {
					$_SESSION['id'] = $row['id'];

					if(isset($_POST['stayloggedin']) AND $_POST['stayloggedin'] == '1'){
						setcookie('id', mysqli_insert_id($link), time() + 60*60*24*365, 'basberg.com');
					}

					header("Location: contribute.php");
					exit();
				}
			}
			
			$error = "<p>Sorry, login failed.</p> ";
			$show_login = true;

		}else if($_POST['sign'] == '2'){


				$query = "SELECT `id` FROM `users` WHERE email = '".mysqli_real_escape_string($link, $_POST['email'])."'LIMIT 1";

						$result = mysqli_query($link, $query);

						if(mysqli_num_rows($result) > 0){

							$row = mysqli_fetch_array($result);
							$id = $row['id'];
					
							$query = "UPDATE `users` SET password = '". md5(md5($id).$_POST['password'])."' WHERE id = '".$id."' LIMIT 1";

							if(mysqli_query($link, $query)){

								$success = "<p>password change successful please log in your account.</p>";
							}

						}else{

							$error = "<p>Email does't exist..please sign up</p>";
						}

			}

		}		


}


?><!DOCTYPE html>
<html>
<head>
<title><?php echo $config['title']; ?></title>
<link rel="stylesheet" href="bootstrap/css/bootstrap.css" >
<link rel="stylesheet" type="text/css" href="support/style.css">
<script type="text/javascript" src="support/jquery.min.js"></script>
	
<meta charSet="utf-8"/>
<meta http-equiv="x-ua-compatible" content="ie=edge"/>
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>

</head>
<body>

<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
	  <a href="<?php echo $config['readingURL']; ?>" class="navbar-brand"><?php echo $config['headline']; ?></span>
	  <a href='<?php echo $config['readingURL']; ?>' class="btn btn-success navRight">Go Back</a>
</nav>

<div id="header">

	<div id="signUp">	
<div id="heading">
<h1 class="display-4">Sign Up</h1>
<small id="emailHelp" class="form-text text-muted"><b>To reduce spam, you have to signup - sorry.</b></small>
</div>

<div id="error" >

<?php 
	if($error != ""){
		echo "<div id='error' class='alert alert-danger' role='alert'><strong>".$error."</strong></div>" ;
		
	} elseif ($success !="") {
		echo "<div id='error' class='alert alert-success' role='alert'><strong>".$success."</strong></div>" ;	
	}
?>		

</div>
			<form method="post">
				  <div class="form-group">
					<input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email"<?php
				
				if (!$show_login) {
					echo "autofocus";
				}
					
					 ?>>
				  </div>

				  <div class="form-group">
					<input type="password" name="password" class="form-control" id="exampleInputPassword1" minlength="6" placeholder="Password">
				  </div>

				  <div class="form-group">
					<input type="password" name="ConfirmPassword" class="form-control" id="exampleInputPassword1" placeholder="Confirm Password">
				  </div>

					  
				<div class="form-group">
					<p><?php echo $config['securityQuestion']; ?></p>
					<input type="text" name="Security" class="form-control" id="exampleInputPassword1" placeholder="First Word Only">
				  </div>
				  
				  
				  <input type="hidden" name="sign" value="1" >
				  <button type="submit" name="submit" class="btn btn-success">Sign Up</button>
			</form>

			<p id="loginInstead">
				Already registered? <a href="#" id="clickLogIn">Please log in</a>
			</p>
	</div>

	<div id="logIn">	
		
		<div id="heading">
			<h1 class="display-4">Login</h1>
			<small id="emailHelp" class="form-text text-muted"><b>Thanks for doing this, it means a lot!</b></small>
		</div>

		<div id="error" >
	
			<?php 
				if($error != ""){
					echo "<div id='error' class='alert alert-danger' role='alert'><strong>".$error."</strong></div>" ;
				}else if($success !=""){
					echo "<div id='error' class='alert alert-success' role='alert'><strong>".$success."</strong></div>" ;	
				}
			?>		

		</div>

			<form method="post">
				  <div class="form-group">
					<input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email"<?php
					
					if ($show_login) {
						echo "autofocus";
					}
						
						 ?>>
				  </div>

				  <div class="form-group">
					<input type="password" name="password" class="form-control" id="exampleInputPassword1" minlength="6" placeholder="Password">
				  </div>

				  <div class="form-check">
					<label class="form-check-label">
					  <input type="checkbox" name="stayloggedin" value="1" class="form-check-input" checked>
					  Remember me
					</label>
				  </div>
				  <input type="hidden" name="sign" value="0" >
				  <button type="submit" name="submit" class="btn btn-success">Log In</button>
			</form>

			<p><a href="#" id="changePassword"> Forgot password?</a></p>
			
			<p id="loginInstead">
				<b>Need to register? <a href="#" id="clickSignup">Please sign up</a></b>
			</p>

	</div>
	<div id="forgetpassword">
		<div id="heading">
			<h1 class="display-4">Reset Password</h1>
		</div>
		
			<form method="post" >
				  <div class="form-group">
					<input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
				   </div> 
				   <div class="form-group">
					<input type="password" name="password" class="form-control" id="exampleInputPassword1" minlength="6" placeholder="New Password">
				  </div>
				  <input type="hidden" name="sign" value="2" >
				  <button type="submit" name="forgetSubmit" class="btn btn-success">Submit</button>
			</form>	
	</div>				
</div>

<script src="bootstrap/js/bootstrap.js"></script>
<script type="text/javascript">

	<?php 
	
	if ($show_login) {
		echo '
		$("#signUp").hide();
		$("#logIn").show();
		$("#error").val("");
		';			
	}
	
	?>

	$("#clickLogIn").click(function(){
		$("#error").hide();
		$("#signUp").toggle();
		$("#logIn").toggle();
		$("#error").val("");
	})
	
	$("#clickSignup").click(function(){
		$("#error").hide();
		$("#signUp").toggle();
		$("#logIn").toggle();
		$("#error").val("");

	})

	$("#changePassword").click(function(){
		$("#logIn").toggle();
		$("#forgetpassword").toggle();
		$("#error").val("");
	})

</script>

</body>
</html>