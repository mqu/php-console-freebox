<?php

error_reporting(E_ALL);

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

require_once('Enregistrement.php');
require_once('EnregistrementFreebox.php');

$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

# $date = '12/03/2011';
$date = date('d/m/Y');

$enreg = new Enregistrement();
$enreg->date    = $date;
$enreg->heure   = 23;
$enreg->minutes = 57;
$enreg->duree   = 5;

# $enreg->repeat   = array(1,2,3);

$enreg->chaine   = 'France 3';   # si PB accent, remplacer par expression reg (ex : 'Public S.*nat')
$enreg->qualite  = 'standard';   # auto, standard, bas-débit, auto, TNT, TNT-HD (idem accents).
$enreg->emission = 'F2 -test';

# lancer l'enregistrement.
$magneto->programmer($enreg);

# lister les enregistrements
foreach($magneto->lister() as $enreg)
	echo $enreg . "\n";
	
?>
