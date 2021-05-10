<?php

@include_once('connection.php');

session_start();

class Admin {
	
	function showInfoAlways() {
		// false: story statistics will only be shown if a valid administrator is logged in.
		// true: will show the story statistics to everyone.
		return true;
	}
	
	function allowAuthentication() {
		// false: no form will be shown
		// true: will show login form if no cookie contains the userID.
		return false;
	}
	
	function isAdmin() {	
		global $link, $userID, $config;
	
		$userID = mysqli_real_escape_string($link, $_SESSION['id']);
		
		if (!strlen($userID)) {
			return false;
		}
		
		// Fetching logged in user story:
		
		$query = "SELECT `isAdmin` FROM `users` WHERE id = $userID LIMIT 1";
		
		$result = mysqli_query($link, $query);		
		$row = mysqli_fetch_array($result);
		
		$isAdmin = $row['isAdmin'] == '1' ? true : false;	
		
		return $isAdmin;
	}
	
	function authenticate() {
		global $_POST, $link;
		
		if(!isset($_POST['email'])) {
			return false;
		}
		
		$email = mysqli_real_escape_string($link, $_POST['email']);

		$query = "SELECT * FROM `users` WHERE email = '$email' LIMIT 1";
		
		$result = mysqli_query($link, $query); 
		$row = mysqli_fetch_array($result);
		
		if(isset($row)){
		
			$codedPassword = md5(md5($row['id']).$_POST['password']);
		
			if($codedPassword == $row['password'] && $row['isAdmin'] == '1') {
		
				$_SESSION['id'] = $row['id'];		
				setcookie('admin', mysqli_insert_id($link), time() + 60*60*24*365);
				
				return true;
			}
		}
		
		// Default response
		return false;	
	}
	
	function adminPanel() {
		global $link;
		
		$queries = [
			['sql' => 'SELECT COUNT(*) AS `count` FROM `users`', 'name' => 'users'],
			['sql' => 'SELECT COUNT(*) AS `count` FROM `users` WHERE `isPublished` = 1', 'name' => 'published'],
			['sql' => 'SELECT COUNT(*) AS `count` FROM `users` WHERE `message` IS NOT NULL', 'name' => 'stories']
		];
		
		$results = Array();

		foreach($queries as $query) {		
			$result = mysqli_query($link, $query['sql']); 
			$row = mysqli_fetch_array($result);
		
			$results[$query['name']] = $row['count'] ?? 'N/A';
		}
			
		$publishedCount = $results['published'];
		$storyCount = $results['stories'];
		$userCount = $results['users'];
		
		return "
		<div class=\"story\" id=\"admin\">
			<div>
				<div class=\"column\">
					WRITERS<br/><em>$userCount</em>
				</div>
				<div class=\"divider\"></div>
				<div class=\"column\">
				STORIES<br/><em>$storyCount</em>
				</div>
				<div class=\"divider\"></div>
				<div class=\"column\">
				PUBLISHED<br/><em>$publishedCount</em>
				</div>			
			</div>			
		</div>";
	}
	
	function panel() {
		global $_GET, $_POST, $_SESSION, $_COOKIE;
		
		if (Admin::showInfoAlways()) {
			return Admin::adminPanel();
		}

		$isVerifiedAdmin = (array_key_exists('id', $_SESSION) && $_SESSION['id']) || (array_key_exists('admin', $_COOKIE) && $_COOKIE['admin']);
		
		if(!$isVerifiedAdmin && !isset($_GET['admin']) && !isset($_POST['admin'])) {
			// Not previously verified as admin, nor is the user trying to login as an admin.
			// Don't show neither admin login nor panel.
			return '';
			
		} elseif ($isVerifiedAdmin || Admin::authenticate()) {			
			// Not previously verified as admin, but successfully logged in as admin!
			return Admin::adminPanel();			
			
		} elseif (Admin::allowAuthentication()) {
			
			// Not already recognised as an admin, but from to login as admin has been requested.
			return '
				<div class="story" id="adminform">
					<div>
						<form method="post">
							<input type="hidden" name="admin" value="1" >
							<div class="form-group">
								<h1>Admin Login</h1>
								<input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
								<input type="password" name="password" class="form-control" id="exampleInputPassword1" minlength="6" placeholder="Password">
							</div>
							<div>
								<button type="submit" name="submit" class="btn btn-success">Authenticate</button>
							</div>
						</form>
					</div>
				</div>
			  ';
		} else {
			return '';
		}
	}
	
}

?>