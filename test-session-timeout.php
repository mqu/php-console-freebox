<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

require_once('Enregistrement.php');
require_once('EnregistrementFreebox.php');

$magneto = new ConsoleMagneto();
# $magneto->login($config['user'], $config['passwd']);
# printf("id = %s\n", $magneto->id());
# printf("idt = %s\n", $magneto->idt());

$magneto->set_from_session('208142', '3dc6a6eaeab62b55');
# 208142&idt=3dc6a6eaeab62b55
# print_r($magneto->lister());
# print_r($magneto->infos_chaines());

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

/*
# permet de mesurer la durée des sessions sur le site Free/console admin.
for($i=18 ; $i<100 ; $i++){
	try {
		
		foreach($magneto->lister() as $enreg)
			echo $enreg . "\n";
			
		echo "sleeping $i x 10\n";
		sleep($i*10);

	} catch(Exception $e){
		echo "exception de timeout session\n";
		$magneto->login($config['user'], $config['passwd']);
	}
}

*/


?>
