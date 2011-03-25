<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

# $liste = $magneto->liste_boxes();
# $liste = $magneto->liste_enregistrements();
# $infos = $magneto->details();
# print_r($magneto->infos_disks());
$infos = $magneto->infos_chaines();
$chaine = $infos->find_by_name('France 2');
$id_chaine = $chaine->services_default_id('.*auto.*');

# $date = '12/03/2011';
$date = date('d/m/Y');


$args1 = array(
	'chaine'    => 2,
	'date'      => $date,
	'duree'	    => 36,
	'emission'	=> 'F2 - journal - 12h',
	'heure'     => 12,
	'minutes'   => 57,
	'service'	=> 1391,
	'where_id'  => 2       # disque dur local de la freebox
);

$args2 = array(
	'chaine'    => 2,
	'date'      => $date,
	'duree'	    => 36,
	'emission'	=> 'F2 - journal - 20h',
	'heure'     => 19,
	'minutes'   => 57,
	'service'	=> 1391,
	'where_id'  => 2       # disque dur local de la freebox
);

$magneto->programmer($args1);
$magneto->programmer($args2);

?>
