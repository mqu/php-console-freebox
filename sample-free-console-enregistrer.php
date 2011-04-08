<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

$infos = $magneto->infos_chaines();
$chaine = $infos->find_by_name('France 2');

# $date = '12/03/2011';
$date = date('d/m/Y');


$args = array(
'chaine' => $chaine->id(),
'date' => $date,
'duree' => 36,
'emission' => 'F2 - journal - 12h',
'heure' => 12,
'minutes' => 57,
'service' => $chaine->services_id('.*auto.*'),
'where_id' => 2 # disque dur local de la freebox
);

$magneto->programmer($args);

$args = array(
'chaine' => $chaine->id(),
'date' => $date,
'duree' => 36,
'emission' => 'F2 - journal - 20h',
'heure' => 19,
'minutes' => 57,
'service' => $chaine->services_id('.*auto.*'),
'where_id' => 2 # disque dur local de la freebox
);

$magneto->programmer($args);

?>
