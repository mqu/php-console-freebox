<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

require_once('Enregistrement.php');
require_once('EnregistrementFreebox.php');


$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

$liste = $magneto->liste_enregistrements();

foreach($liste as $enreg)
	echo $enreg;


?>
