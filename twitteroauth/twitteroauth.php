<?php
/*
 * Abraham Williams (abraham@abrah.am) http://abrah.am
 *
 * The first PHP Library to support OAuth for Twitter's REST API.
 *
 * Modified by Rob Marscher (rob@robmarscher.com) to use the pecl/oauth extension
 * and adds use of the Authorization header and enables profile/background image uploading
 */

if (!extension_loaded('oauth')) {
	throw new Exception("This Twitter OAuth library requires the pecl/oauth extension");
}

/**
 * Twitter OAuth class
 */
class TwitterOAuth {
  /**
   * @var OAuth
   */
  public $consumer;

  /* Contains the last API call. */
  public $url;

  /* Set up the API root URL. */
  public $host = "https://api.twitter.com/1/";

  /* Verify SSL Cert. */
  public $ssl_verifypeer = false;

  /* Respons format. */
  public $format = 'json';

  /* Decode returned json data. */
  public $decode_json = true;

  /* Set the useragent. */
  public $useragent = 'TwitterOAuth v0.2.0-beta2';

  /* Immediately retry the API call if the response was not successful. */
  public $retry = true;

  /* Number of times to retry the API call if the response was not successful. */
  public $retryAttempts = 3;

  /* Number of times the current request has been retried. */
  public $currentRetries = 0;

  /**
   * Set API URLS
   */
  const ACCESS_TOKEN_URL = 'https://api.twitter.com/oauth/access_token';
  const AUTHENTICATE_URL = 'https://twitter.com/oauth/authenticate';
  const AUTHORIZE_URL = 'https://twitter.com/oauth/authorize';
  const REQUEST_TOKEN_URL = 'https://api.twitter.com/oauth/request_token';

  /**
   * construct TwitterOAuth object
   */
  public function __construct($consumer_key, $consumer_secret, $oauth_token = null, $oauth_token_secret = null, $enable_debug = false) {
    $this->consumer = new OAuth($consumer_key, $consumer_secret, OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_AUTHORIZATION);
    if (!empty($oauth_token) && !empty($oauth_token_secret)) {
      $this->consumer->setToken($oauth_token, $oauth_token_secret);
      $this->token = array($oauth_token, $oauth_token_secret);
    } else {
      $this->token = null;
    }
  }

  /**
   * Turns on debug for the OAuth consumer
   */
  public function enableDebug() {
    $this->consumer->enableDebug();
  }

  /**
   * Returns the collected debug info (only if enableDebug was called first)
   * 
   * @return array
   */
  public function getDebugInfo() {
    return $this->consumer->debugInfo;
  }

  /**
   * Get a request_token from Twitter
   *
   * @return array a key/value array containing oauth_token and oauth_token_secret
   */
  public function getRequestToken($oauth_callback = null) {
    $this->token = $this->consumer->getRequestToken(self::REQUEST_TOKEN_URL, $oauth_callback);
    return $this->token;
  }

  /**
   * Get the authorize URL
   *
   * @return string
   */
  public function getAuthorizeURL($token, $sign_in_with_twitter = true) {
    if (is_array($token)) {
      $token = $token['oauth_token'];
    }
    if (empty($sign_in_with_twitter)) {
      return self::AUTHORIZE_URL . "?oauth_token={$token}";
    } else {
      return self::AUTHENTICATE_URL . "?oauth_token={$token}";
    }
  }

  /**
   * Exchange request token and secret for an access token and
   * secret, to sign API calls.
   *
   * @return array("oauth_token" => "the-access-token",
   *               "oauth_token_secret" => "the-access-secret",
   *               "user_id" => "9436992",
   *               "screen_name" => "abraham")
   */
  public function getAccessToken($oauth_verifier = false) {
    $token = $this->consumer->getAccessToken(self::ACCESS_TOKEN_URL, null, $oauth_verifier);
    if ($token !== false) {
    	$this->token = $token;
    	$this->consumer->setToken($token['oauth_token'], $token['oauth_token_secret']);
    }
    return $token;
  }

  /**
   * Returns the default HTTPHeaders for the OAuth client
   *
   * @return array
   */
  public function getHTTPHeaders()
  {
    return array(
    	'User-Agent' => $this->useragent
    );
  }

  /**
   * GET wrapper for oAuthRequest.
   * @return object
   */
  public function get($url, $parameters = array()) {
    return $this->fetch($url, $parameters, OAUTH_HTTP_METHOD_GET);
  }
  
  /**
   * POST wrapper for oAuthRequest.
   */
  public function post($url, $parameters = array()) {
    return $this->fetch($url, $parameters, OAUTH_HTTP_METHOD_POST);
  }

  /**
   * DELETE wrapper for oAuthReqeust.
   */
  public function delete($url, $parameters = array()) {
    return $this->fetch($url, $parameters, OAUTH_HTTP_METHOD_DELETE);
  }

  /**
   * Abstracts calling OAuth::fetch
   */
  protected function fetch($url, $parameters = array(), $method = OAUTH_HTTP_METHOD_GET) {
    $result = $this->consumer->fetch(
      $this->normalizeUrl($url),
      $parameters,
      $method,
      $this->getHTTPHeaders()
    );
    if ($result === true) {
      $response = $this->consumer->getLastResponse();
      $this->currentRetries = 0;
      if ($this->format === 'json' && $this->decode_json) {
        return json_decode($response);
      }
      return $response;
    } else if ($this->retry) {
      if ($this->currentRetries < $this->retryAttempts) {
        $this->currentRetries++;
        $this->fetch($url, $parameters, $method);
      }
    }
    $this->currentRetries = 0;
	throw new OAuthException("Twitter returned an error for " . $url);
  }

  /**
   * Adds on the baseurl and format extension if they don't already exist 
   *
   * @param string $url
   * @return string
   */
  public function normalizeUrl($url)
  {
    if (strrpos($url, 'https://') !== 0 && strrpos($url, 'http://') !== 0) {
      $url = "{$this->host}{$url}.{$this->format}";
    }
    return $url;
  }
}
