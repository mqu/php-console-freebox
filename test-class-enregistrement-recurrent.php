<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

require_once('Date.php');
require_once('EnregistrementRecurrent.php');

# $date = '12/03/2011';
$date = Date::today();

$enreg = new EnregistrementRecurrent();
$enreg->date    = $date;
$enreg->heure   = 19;
$enreg->minutes = 57;
$enreg->duree   = 36;

# répetition : 0: dimanche, 1:lundi, ...
$enreg->repeat   = array(0,2);

$enreg->chaine   = 'France 2';
$enreg->qualite  = 'standard';   # auto, standard, bas-débit, auto, TNT, TNT-HD
$enreg->emission = 'F2 - journal - 20h -test';

foreach ($enreg->repeat($count=30) as $event)
	echo $event;

?>
