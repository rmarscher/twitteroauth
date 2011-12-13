<?php

/**
 * @file
 * A single location to store configuration.
 */

// option to create an additional file containing the twitter credentials that's not in source control.
if (file_exists(__DIR__ . '/config.local.php')) {
	require __DIR__ . '/config.local.php';
} else {
	// delete the next line if you put your twitter credentials here
	die("You must edit config.php or create a config.local.php file with your twitter application key and secret.\n");
	define('CONSUMER_KEY', '');
	define('CONSUMER_SECRET', '');
}
if (isset($_SERVER['REQUEST_URI'])) {
	// try to guess where we're at
	$_baseurl = dirname($_SERVER['REQUEST_URI']);
	if ($_baseurl === '/') {
		$_baseurl = '';
	}
	switch ($_SERVER['SERVER_PORT']) {
		case 80:
			$_baseurl = "http://" . $_SERVER['HTTP_HOST'] . $_baseurl;
		break;
		case 443:
			$_baseurl = "https://" . $_SERVER['HTTP_HOST'] . $_baseurl;
		break;
		default:
			$_baseurl = "http://" . $_SERVER['HTTP_HOST'] . ":" . $_SERVER['SERVER_PORT'] . $_baseurl;
		break;
	}
	if ($_baseurl === 'http:' || $_baseurl === 'https:') {
		$_baseurl == $_SERVER['REQUEST_URI'];
		if (strrpos($_baseurl, '/') !== false) {
			$_baseurl = substr($_baseurl, 0, -1);
		}
	}
	define('OAUTH_CALLBACK', $_baseurl . '/callback.php');
} else {
	define('OAUTH_CALLBACK', 'http://localhost/twitteroauth/callback.php');
}
