<?php
	/*
	    The MIT License (MIT)

	    Copyright (c) 2014 Oliver Moran, and 2018 github.com/vjcharles changed API &
	    modified as an API-Proxy.

	    Permission is hereby granted, free of charge, to any person obtaining a copy of
	    this software and associated documentation files (the "Software"), to deal in
	    the Software without restriction, including without limitation the rights to
	    use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
	    of the Software, and to permit persons to whom the Software is furnished to do
	    so, subject to the following conditions:

	    The above copyright notice and this permission notice shall be included in all
	    copies or substantial portions of the Software.

	    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
	    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
	    SOFTWARE.
	*/

	// Used to enable CORS calls and hide your secrets from the client making the request.
	// Example: index.php?http://www.example.org/resource.json?count=1&length=full

        /////////////////////////////////////////
        // Configuration
	// =============
	//
	// Supported Domains and respective parameters to add.
	$whitelist = array(
		"airnowapi.org" => array(
			"API_KEY" => "SOME_API_KEY",
		),
	);	

	// enable access from all domains. Comment this out if you want to only allow your clients making cors requests
	enable_cors();

	// end Configuration
        /////////////////////////////////////////

        //echo print_r($_SERVER["REQUEST_URI"]);
	//$url = $_REQUEST["url"];
	$request_uri = $_SERVER["REQUEST_URI"];
	$request_split = preg_split("/php\?/", $request_uri);
	if ($request_split[1]) {
		$url = $request_split[1]; 
	} else {
		die("ERROR: The url must include a single parameter of the URL to proxy. Ex. ...php?https://your_api_call.com?json");
	}
	
	if (substr ($url, 0, 7) != "http://"
		&& substr ($url, 0, 8) != "https://"
		&& substr ($url, 0, 6) != "ftp://") {
		// NB: only absolute URLs are allowed -
		// otherwise the script could be used to access local-to-file system files
		die("ERROR: The argument 'url' must be an absolute URL beginning with 'http://', 'https://', or 'ftp://'.");
	}

	if (!is_url_whitelisted($url)) {
		die("ERROR: This url is not whitelisted.");	
	}

	// temporarily override CURLs user agent with the user's own
	ini_set("user_agent", $_SERVER['HTTP_USER_AGENT']);

	$url = add_param($url);

	switch ($_SERVER["REQUEST_METHOD"]) {
		case "GET":
			get($url);
			break;
		default:
			post($url);
			break;
	}


	function is_url_whitelisted($url) {
		global $whitelist;
		$is_whitelisted = false;
		foreach ($whitelist as $key => $value) {
			if (count(explode($key, $url)) > 1) {
				$is_whitelisted = true;
			}
		}
		return $is_whitelisted;
	}

	function get_params($url) {
		global $whitelist;
		$params = array();
		foreach ($whitelist as $key => $value) {
			if (count(explode($key, $url)) > 1) {
				$params = $value;
			}
		}
		return $params;
	}

	// append extra url parameters, like a hidden API key
	// this blows away existing params if already in the url.
	function add_param($url) {
		$url_parts = parse_url($url);
		parse_str($url_parts['query'], $params);
		$new_params = get_params($url);
		if (count($new_params) > 0) {
			$params = array_merge($params, $new_params);
		}
		$url_parts['query'] = http_build_query($params);
		$new_url = $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . '?' . $url_parts['query'];
		return $new_url;
	}

	// get the contents of the URL and echo the results
	function get($url) {
		// if (substr ($url, 0, 8) == "https://") {
		//	echo getSSL($url);
		// } else {
			echo file_get_contents($url);
		// }
	}

	// gets over HTTPS
	function getSSL($url) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, false);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_SSLVERSION,3); 
	    $result = curl_exec($ch);
	    curl_close($ch);
	    return $result[0];
	}

	// post (or put or delete?) the encoded form to the URL and echo the results
	function post($url) {
		$postdata = http_build_query(
		    array()
		);

		$opts = array('http' =>
		    array(
		        'method'  => $_SERVER['REQUEST_METHOD'],
		        'header'  => 'Content-type: application/x-www-form-urlencoded',
		        'content' => $postdata
		    )
		);

		$context  = stream_context_create($opts);

		// get the contents of the external URL and echo it
		echo file_get_contents($url, false, $context);
	}

	/**
	 *  An example CORS-compliant method.  It will allow any GET, POST, or OPTIONS requests from any
	 *  origin.
	 *
	 *  In a production environment, you probably want to be more restrictive, but this gives you
	 *  the general idea of what is involved.  For the nitty-gritty low-down, read:
	 *
	 *  - https://developer.mozilla.org/en/HTTP_access_control
	 *  - http://www.w3.org/TR/cors/
	 *
	 */
	function enable_cors() {
		// Allow from any origin
		if (isset($_SERVER['HTTP_ORIGIN'])) {
			header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
			header('Access-Control-Allow-Credentials: true');;
			header('Access-Control-Max-Age: 86400');	// cache for 1 day
		} else {
			header("Access-Control-Allow-Origin: *");
			header('Access-Control-Allow-Credentials: true');;
			header('Access-Control-Max-Age: 86400');	// cache for 1 day
		}

		// Access-Control headers are received during OPTIONS requests
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
				header("Access-Control-Allow-Methods: GET, POST, OPTIONS");		 

			if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
				header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

			exit(0);
		}
	}
?>
