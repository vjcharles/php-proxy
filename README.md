PHP-Proxy
=========
PHP-API-Proxy

This script allows cross-domain JavaScript AJAX calls using GET and POST (and propably other methods) to any server by acting as a proxy, while hiding API keys from clients.

In an example where JavaScript on `www.webhost.com` would like to make an AJAX request to an resource on `api.server.com` (say `http://api.server.com/resource.json`) but cannot because of cross domain security restrictions, the request can be made via the proxy.

The proxy accepts just the full url as the only parameter. Place the script on a PHP host and from JavaScript call it with something like:

    http://www.phphost.com/php-proxy/index.html?https://api.server.com/resource.json?level=2&count=1

Notice the url after the `?` can include any number of parameters, that are not url-encoded. This API format was inspired by [cors.io]("https://cors.io").

By default, CORS is enabled on the script, meaning any domain can call the script. To limit calls to only your host (recommended), comment out the line `enable_cors();` or modify the script as needed.

## Why?

Because PHP is installed by default on a lot of shared hosting platforms.

Because minimal serverside infrastructure is desired. Simplicity is Queen.

## Configuration

Currently configuration is stored IN THE SCRIPT where you can add a domain you support and its list of needed parameters, usually that will just be 1 API key-value pair.

Comment out the enable_cors() function if you only want your clients to use this script.

## Installation

Put this script on your server that has PHP configured.

## Using from a client

Take your usual api call, remove the secret(s) key-value parameters, prepend the location this script is hosted, get the same results you had prior.

    http://www.phphost.com/php-proxy/index.html?https://api.server.com/resource.json?level=2&count=1

## License

MIT
