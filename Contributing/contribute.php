<?php

	// Remote Location
	@include_once('../../nico/support/config.php');
	@include_once('../../nico/support/messages.php');
	@include_once('../../nico/support/connection.php');
	
	// Local location
	@include_once('../Reading/support/config.php'); 
	@include_once('../Reading/support/messages.php');
	@include_once('../Reading/support/connection.php');
	
	session_start();

	$message = "";
	$savedStories = "";

	if(array_key_exists("id", $_COOKIE) AND $_COOKIE['id']) {

		$_SESSION['id'] = $_COOKIE['id'];
	}

	if(array_key_exists('id', $_SESSION) AND $_SESSION['id']) {

		$userID = mysqli_real_escape_string($link,$_SESSION['id']);
		
		// Fetching logged in user story:

		$query = "SELECT `message`, `isPublished` FROM `users` WHERE id = $userID LIMIT 1";

		$result = mysqli_query($link,$query);
		$row = mysqli_fetch_array($result);
		
		$message = $row['message'];
		$isPublished = ($row['isPublished'] == "1");

	} else {
		header('Location: '. $config['contributionURL']);
	}

?><!DOCTYPE html>
<html>
<head>
	<title><?php echo $config['title']; ?></title>

	<link rel="stylesheet" href="bootstrap/css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="support/style.css">

	<meta charSet="utf-8"/>
	<meta http-equiv="x-ua-compatible" content="ie=edge"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	
</head>
<body>

	<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
		  <a href="<?php echo $config['readingURL']; ?>" class="navbar-brand" id="brandTitle"><?php echo $config['headline']; ?></a>
		  <a href='index.php?logout=1' id="logout">Log out</a>
	</nav>

	<div  id="message">
		<div>
			<p>
				You can use <a target='top' href='https://daringfireball.net/projects/markdown/'>markdown</a> to format the text. For example start a line with <b class="newTag">#</b> to make a title, and <b class="newTag">##</b> for a subtitle.
			</p>
			<p>
				To separate multiple stories, add a line containing only: <b class="newTag">-NEW-</b>
			</p>
			
			<p>
				You can use the following textual emoji's in your story:<br/>
				<em class="entities"><?php echo translateEmoji(null); ?></em>
			</p>
			
			<p>
				You can additionally use <a href="" target="top">any HTML entity code</a> by writing the numerical code like this <b class="entities">::129409;</b> which shows up as &#129409;
			</p>

			<textarea id="text"><?php echo $message ?? $config['placeholder'];  ?></textarea>
		</div>
		<div id="toolbar">	
			<span id="saveDraft" class="btn btn-success">Save Story</span>
			<span id="publishStory" class="btn btn-danger publishedGroup" style="<?php
		
			if ($isPublished) {
					echo 'display: none;';
			} else {
				echo 'display: inline;';
			}
		
		 ?>">Publish Story</span>
			<span id="unpublishStory" class="btn btn-warning unpublishedGroup" style="<?php
			
				if ($isPublished) {
					echo 'display: inline;';
				} else {
					echo 'display: none;';
				}
			
			 ?>">Unpublish Story</span>
		</div>
		
		<span id="preview"<?php
		
		echo ' style="display: inline;">';
		print(previewMessage());			
		
		?></span>
	</div>
	
	<?php
	
	echo '<div id="publishedStories"><a id="toggleStories" href="#publishedStories">Toggle stories from others..</a></div></div>';
	showMessages(true);
	
	?>

	<script type="text/javascript" src="support/jquery.min.js"></script>	
	<script src="bootstrap/js/bootstrap.js"></script>
	<script type="text/javascript">
	
		function showSaveFeedback($autoSaved = false) {
			if ($autoSaved) {
				$("#brandTitle").html("Auto-saved!");
			} else {
				$("#brandTitle").html("Saved!");
			}

			setTimeout(function () {
				$("#brandTitle").html("<?php echo $config['headline']; ?>");
			}, 2000);
		}

		function showPublishedFeedback($published = true) {
			if ($published) {
				$("#brandTitle").html("Published!");
			} else {
				$("#brandTitle").html("Unpublished!");
			}
			
			setTimeout(function () {
				$("#brandTitle").html("<?php echo $config['headline']; ?>");
			}, 2000);
		}
		
		var changeCount = 0;
	
		$("#text").on("change paste keyup", function() {
			if (changeCount == 40) {
				$.ajax({
					method: "POST",
					url: "updateText.php",
					data: { content: $("#text").val() },
					success: function(data){
						if (data != "fail") {
							showSaveFeedback(true);
							changeCount = 0;
							
							if ($("#preview").is(':visible')) {
								$("#preview").html(data);
							}
						}
					}
				});
			}
			changeCount++;
		});
		
		$("#toggleStories").click(function(){
			$(".story").toggle();
		})
		
		$("#saveDraft").click(function(){
			$.ajax({
				method: "POST",
				url: "support/updateText.php",
				data: { content: $("#text").val() },
				success: function(data){
					if (data != "fail") {
						showSaveFeedback();
						changeCount = 0;
						
						if ($("#preview").is(':visible')) {
							$("#preview").html(data);
						}
					}
				}
			});
		})
		
		$("#publishStory").click(function(){
			$.ajax({
				method: "POST",
				url: "support/publish.php",
				data: { content: $("#text").val() },
				success: function(data){
					if (data != "fail") {
						showPublishedFeedback();
						$(".publishedGroup").hide();
						$(".unpublishedGroup").show();
						changeCount = 0;
						
						$("#preview").html(data).show();
					}
				}
			});			
		})
		
		$("#unpublishStory").click(function(){
			$.ajax({
				method: "POST",
				url: "support/publish.php?unpublish=true",
				data: { content: $("#text").val() },
				success: function(data){
					if (data != "fail") {
						showPublishedFeedback(false);
						$(".publishedGroup").show();
						$(".unpublishedGroup").hide();						
						$("#preview").fadeOut().html("");
					}
				}
			});			
		})



	</script>
</body>
</html>