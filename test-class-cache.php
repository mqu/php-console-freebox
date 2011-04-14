<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('Cache.php');

$cache = new Cache();

$url = 'http://www.google.fr/';

switch($test=1){
case 1:
	if(($data=$cache->get($url)) === false){
		echo "not cached\n";
		$data = file_get_contents($url);
		$cache->put($url, $data);
	} else
		echo "cached\n";
	break;
case 2:
	$data=$cache->get($url) or ($data = file_get_contents($url) and $cache->put($url, $data));
	break;
}

print_r($data);

?>
