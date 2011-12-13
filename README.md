TwitterOAuth
============

Rob Marscher | rob@robmarscher.com

Fork of abraham's OAuth library for Twitter's REST API. This version uses the pecl/oauth extension instead of a php OAuth class.

Reasons for forking this project:

* C-extensions are inherently faster than PHP libraries
* pecl/oauth has support of PHP creator Rasmus Lerdorf 
  (see [http://toys.lerdorf.com/archives/50-Using-pecloauth-to-post-to-Twitter.html](http://toys.lerdorf.com/archives/50-Using-pecloauth-to-post-to-Twitter.html))
* pecl/oauth can send OAuth Authorization HTTP headers
* I worked with the pecl/oauth team to add support for multipart form posts 
  that enable uploading profile images and background images to your twitter profile 
  (see [http://pecl.php.net/bugs/bug.php?id=17782](http://pecl.php.net/bugs/bug.php?id=17782))

Original README preserved here:
-------------------------------

Abraham Williams | abraham@poseurte.ch | http://abrah.am | @abraham

The first PHP library for working with Twitter's OAuth API.

Documentation: http://wiki.github.com/abraham/twitteroauth/documentation 
Source: http://github.com/abraham/twitteroauth
Twitter: http://apiwiki.twitter.com
