<?php

include_once('config.php');
include_once('connection.php');
include_once('slimdown.php');

session_start();

function zdateRelative($date)
{
  $diff = time() - $date;
  $periods[] = [60, 1, '%s seconds ago', 'a second ago'];
  $periods[] = [3600, 60, '%s minutes ago', 'one minute ago'];
  $periods[] = [3600*24, 3600, '%s hours ago', 'an hour ago'];
  $periods[] = [3600*24*7, 3600*24, '%s days ago', 'yesterday'];
  $periods[] = [3600*24*30, 3600*24*7, '%s weeks ago', 'one week ago'];
  $periods[] = [3600*24*30*12, 3600*24*30, '%s months ago', 'last month'];
  $periods[] = [INF, 3600*24*265, '%s years ago', 'last year'];
  foreach ($periods as $period) {
	if ($diff > $period[0]) continue;
	$diff = floor($diff / $period[1]);
	return sprintf($diff > 1 ? $period[2] : $period[3], $diff);
  }
}

function translateEmoji($message) {
	$variants = [
		['<3', '&#128149;'],		
		[':)', '&#128512;'],
		[':-)', '&#128513;'],
		[';)', '&#128521;'],
		[';-)', '&#128540;'],
		[':(', '&#128543;'],
		[':-(', '&#128549;'],
		[":'(", '&#128546;'],
		[":'‑(", '&#128557;'],
		['8-o', '&#128561;']
	];
	
	$actualMessage = (!empty($message));
	$spacer = '';
	
	foreach($variants as $variant) {
		if ($actualMessage) {
			$message = str_replace($variant[0], $variant[1], $message);
		} else {
			$message .= $spacer . '<span class="emoji">'. $variant[0] .'</span>&nbsp;'. $variant[1];
			$spacer = '</em> <em class="entities">';
		}
	}

	// Supporting any HTML entity code:	
	$pattern = '/::(\d+);/';
	$replacement = '&#$1;';
	return preg_replace($pattern, $replacement, $message);	
}

function formattedDiaryEntry($created, $message, $isPreview = false) {
	$phpdate = strtotime($created);

	if ($isPreview) {
		$class = "preview";
	} else {
		$class = "story";
	}
	
	$relativeDate = zdateRelative($phpdate);
	$storyDivider = '<p class="timestamp">'. $relativeDate . '</p></div></div><div class="'. $class .'"><div>';

	$split_stories = str_replace("-NEW-", $storyDivider, $message);
	
	$formatted = Slimdown::render($split_stories);	
	$formatted = translateEmoji($formatted);
	
	$result = '<div class="'. $class .'"><div>';	
	$result .= $formatted;
	$result .= '<p class="timestamp">'. $relativeDate .'</p></div></div>';
	
	return $result;
}

function previewMessage() {	
	global $link, $userID, $config;

	$userID = mysqli_real_escape_string($link,$_SESSION['id']);
	
	// Fetching logged in user story:
	
	$query = "SELECT `message`, `created` FROM `users` WHERE id = $userID LIMIT 1";
	
	$result = mysqli_query($link, $query);
	
	$row = mysqli_fetch_array($result);
	
	$story = $row['message'] ?? $config['placeholder'];
	
	return formattedDiaryEntry($row['created'], $story, true);
}

function showMessages($excludeLoggedInUser = false) {	
	global $link, $userID;

	if ($excludeLoggedInUser) {
		$query = "SELECT `message`, `created` FROM `users` WHERE id != $userID AND `isPublished` = 1 ORDER BY `created` DESC";
	} else {
		$query = "SELECT `message`, `created` FROM `users` WHERE `isPublished` = 1 ORDER BY `created` DESC";
	}
	
	$result = mysqli_query($link, $query);
		
	while($row = mysqli_fetch_array($result)) {
		print(formattedDiaryEntry($row['created'], $row['message']));
	}
}

?>