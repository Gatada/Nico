<?php

// CONFIGURATION FILE
// This configuration file will need to be updated to your needs.

// The global configuration container.
$config = Array();

// A file I have in my project folder that is not committed to GitHub.
@include_once('secrets.php');

if (isset($secrets)) {
	
	$config = $secrets;
	$config['ingress'] = $secrets['ingress']['first'] . $config['contributionURL'] . $secrets['ingress']['second'];
	unset($secrets);
	
} else {
	
	// CONFIG DEPENDENT ON RUNNING ENVIRONMENT
	// ----------------------------------------------------------------------------------------
	
	// A boolean that is true if the script is running on a Mac.
	// Update this as needed. For my setup, my public web server does not have a root path that
	// starts with /Users/ - however, you might need to update this to something that is unique
	// to your local machine (and different from the public server).
	$config['isLocalhost'] = (strpos(getcwd(), '/Users/') === 0);
	
	// Selecting a configuration depending on what environment is running the scripts:
	if ($config['isLocalhost']) {
		
		// Local MacBook Pro
		// --------------------------------------------
		$config['contributionURL'] = 'http://localhost/writing/';
		$config['readingURL'] = 'http://localhost/reading/';
		$config['relativePath'] = '../reading/';
		
		// MySQL Server Setup
		$config['db'] = [
			'host' => '127.0.0.1',
			'user' => 'admin',
			'password' => 'password',
			'name' => 'db_nico'
		];	
		
	} else {
		
		// Remote
		// --------------------------------------------
		$config['contributionURL'] = "https://yourserver.com/writing/";
		$config['readingURL'] = "http://reading.yourserver.com/";
		$config['relativePath'] = '../reading/';
		
		// MySQL Server Setup
		$config['db'] = [
			'host' => 'nico.yourISP.com',
			'user' => 'admin',
			'password' => 'password',
			'name' => 'db_nico'
		];

	}
	
	// TEXTUAL ELEMENTS
	// ----------------------------------------------------------------------------------------
	
	// Bookmark title
	$config['title'] = 'Shared Stories';
	
	// Headline shown on the page in the navigation bar
	$config['headline'] = 'Our Collective Stories';
	
	
	$config['securityQuestion'] = 'Please enter the last word in Top Secret:';
	
	// The answer to the security question (lower-case, check is case-insensitive)
	$config['secretWord'] = 'secret';
	
	// The placeholder text added to the text area when the user
	// hasn't written a story yet (content is null).
	$config['placeholder'] = "# Placeholder Title\n\nFirst paragraph.\n\nSecond paragraph.";
	
	// The ingress shown at the top of the reading page:
	
	$config['ingress'] = '<p>
	<b>Hey, and welcome!</b>
	</p>
	<p>
		This is a site where each user can share a single story with the world. The newest stories are shown first. I developed this as a digital memorial service to keep the stories of my brother alive - but of course, use it for whatever you want.
	</p>
	<p>
	If you have a story, a memory or encounter to share that you want to share then please <a href="'. $config['contributionURL'] .'" class="font-weight: bold;">contribute</a>. You can write in any language you want.
	</p>
	<p>
	Your story is shown as written. Nobody will know who wrote it unless you add your name to it - feel free to keep it a secret.
	</p>
	<p id="signed">
	Thanks!
	</p>
	<p class="timestamp">Created May 2021</p>';

}

?>