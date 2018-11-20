PHP-API-Proxy
=============

This script allows CORS calls using GET and POST (and propably other methods) to any server by acting as a proxy, while hiding API keys from clients.

## Why?

Because a proxy is better when it can hide api keys from clients.

Because PHP is installed by default on a lot of shared hosting platforms.

Because minimal server-side infrastructure is desired. Simplicity is Queen!


## Configuration

Currently configuration is stored in `config.ini.php` where you can add a domain you support and its list of needed parameters. See config file for example.
Update the location of the configuration file in `api-proxy.php`.

By default, CORS is enabled on the script, meaning any domain can call the script. To limit calls to only your host (recommended), comment out the line `enable_cors();`.

Comment out the enable_cors() function if you only want your clients to use this script.


## Installation

Put the config file just above the root of your app. 

Put this script on your server running PHP.


## Using from a client

The proxy accepts just the full url as the only parameter. Place the script on a PHP host and from JavaScript call it with something like:

    http://www.phphost.com/api-php.html?https://api.server.com/resource.json?level=2&count=1

Notice the url after the `?` can include any number of parameters, that are not url-encoded. 

This API format was inspired by [cors.io]("https://cors.io").


## Differences between PHP-Proxy and PHP-API-Proxy

1. There is no `?url=` param now, just pass the entire url with all it's params. This allows for no url-encoding tedium.
1. This proxy adds `config.ini.php` configuration file of white list and params to add to proxied calls.
   This allows for making API calls from a client without revealing the api key to the client.


## A word of warning / Security disclaimer

There may be better ways to secure your secrets. The intention here is to not use external libraries, and be as close to just dropping a single file onto a server to get this new functionality. The use of an .ini file is to use a built-in PHP configuration standard.

Putting this configuration file above the website's root is critical. Other methods include an .htaccess file. The config file is formatted as a valid php file, this is a method for short-circuiting a config file that is accidentally readable from the web. 

## License

MIT
