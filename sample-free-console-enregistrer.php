<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

require_once('Enregistrement.php');
require_once('EnregistrementFreebox.php');

$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

$infos = $magneto->infos_chaines();
$chaine = $infos->find_by_name('France 2');

# $date = '12/03/2011';
$date = date('d/m/Y');

$enreg = new Enregistrement();
$enreg->date    = $date;
$enreg->heure   = 19;
$enreg->minutes = 57;
$enreg->duree   = 36;

$enreg->chaine = $chaine;
$enreg->emission = 'F2 - journal - 20h -test';
$enreg->date = $date;

# lancer l'enregistrement.
$magneto->programmer($enreg);

# lister les enregistrements
$liste = $magneto->liste_enregistrements();
foreach($liste as $enreg)
	echo $enreg;

?>
