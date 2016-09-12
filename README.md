# PokemonGo-Map-GUI-PHP
A PHP implementation of <a href="https://github.com/PokemonGoMap/PokemonGo-Map/">PokemonGo-Map</a>'s GUI.

This implementation is fairly basic. I run my map with a fixed location and search control disabled, so such things have been disabled or not written in the code.

<h3>Requirements:</h3>
<ul><li>Existing MySQL database created by PokemonGo-Map</li>
<li>PHP >= 5.4 (with <b>mod_rewrite</b> enabled)</li></ul>

<h3>Setting up config.inc.php</h3>
$startingLat and $startingLng
-----------------------------
These coordinates mark where the "fixed location" for PokemonGo-Map is. Location settings are still available to start at the user's location. NOTE: Geolocation using the Google Maps API requires a secure origin. If you're running this setup without an SSL certificate, then your browser options are limited if you want geolocation to work. Firefox seems to work nicely without SSL.

$dbHost, $dbName, $dbUser, and $dbPass
--------------------------------------
These are the credentials for your MySQL database. I didn't add any support for other database types, since MySQL is my preference.

$title
------
This marks the title that is shown in the title bar when visiting your map.

$locale
-------
This is mostly for aesthetics and for reference should you want to implement language changes. With the code how it is, it only affects the lang specifier in the HTML. If you'd like it to change languages based on this option, you'll need to implement the code necessary to do it. To do so, refer to the i8ln(word) function in 'utils.py' from PokemonGo-Map and the get_pokemon_data($pokemon_id) function in 'utils.php' from this implementation

$gmapsKey
---------
This is your Google Maps API key.

$purgeData
----------
If you don't want the map to purge old pokemon data, then leave this option at 0. Otherwise, input a higher number to delete pokemon spawns older than X hour(s). X is the number you'd place here.


Take care to leave all existing quotations intact. Once you've edited 'config.inc.php' to your liking, simply upload all the files to your web server, and it's good to go. That's it! It doesn't get any simpler. Keep in mind that I wrote this to fulfill my needs, and it may be missing some things that the official GUI has. It also doesn't include any functionality to control workers in any way, since I prefer spawnpoint scanning in certain areas. I do not plan on extending the functionality of this implementation any further than it currently is. If you'd like to, feel free. :)


<h3>Configure ReWrite rules if you use NGINX</h3>
-----------------------------
Add the following line to your nginx configuration:

    server {
      ...
      rewrite ^/raw_data$ /app.php?func=raw_data$1 last;
    	rewrite ^/loc$ /app.php?func=loc$1 last;
    	rewrite ^/next_loc$ /app.php?func=next_loc$1 last;
    	rewrite ^/mobile$ /app.php?func=mobile$1 last;
    	rewrite ^/search_control$ /app.php?func=search_control$1 last;
    	rewrite ^/stats$ /statistics.php$1 last;
      ...
    }


-----------------------------
<h3>Trobuleshooting</h3>
If you are having any problems with the map, please check the following:

<h3>Apache Virtual Host Config</h3>
-----------------------------
Make sure you have these setting in your Apache Virtual Host Config, otherwise map will not load correctly.

     Options FollowSymLinks
     AllowOverride All

So you should have a config that looks something like this:

    <VirtualHost *:80>
    DocumentRoot "/var/www/test"
    ServerName test.domain.com
    <Directory "/var/www/test">
     allow from all
     Require all granted
     Options FollowSymLinks
     AllowOverride All
    </Directory>
    </VirtualHost>
-----------------------------
<b>Credits:</b>
<a href="https://github.com/PokemonGoMap/PokemonGo-Map/">PokemonGo-Map</a>
