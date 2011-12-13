<?php

/* Start session and load library. */
session_start();
require_once dirname(__DIR__) . '/TwitterOAuth.php';
require_once __DIR__ . '/config.php';

use twitteroauth\TwitterOAuth;

/* Build TwitterOAuth object with client credentials. */
$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
$connection->enableDebug(); // uncomment to collect additional debug info

/* Get temporary credentials. */
try {
	$request_token = $connection->getRequestToken(OAUTH_CALLBACK);
	/* Save temporary credentials to session. */
	$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
    /* Build authorize URL and redirect user to Twitter. */
    $url = $connection->getAuthorizeURL($token);
    header('Location: ' . $url);
} catch (\OAuthException $e) {
	$responseInfo = $connection->getLastResponseInfo();
    echo "Could not connect to Twitter ({$e->getMessage()}). Refresh the page or try again later.";
	///* uncomment for some additional debug info
	echo "<pre>Response:\n" . print_r($responseInfo, true) . "\n\nSESSION:\n" . print_r($_SESSION, true) . "\n" .
		print_r($connection->getDebugInfo(), true) . "\n</pre>\n";
	//*/
}
