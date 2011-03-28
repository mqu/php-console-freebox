<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

$infos = $magneto->infos_chaines();

# print_r($infos);
echo $infos;

?>
