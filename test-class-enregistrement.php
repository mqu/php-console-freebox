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

# aujourd'hui / format (12/03/2011)
$date = date('d/m/Y');

$args = array(
	'date'      => $date,
	'duree'	    => 5,
	'emission'	=> 'test',
	'heure'     => 23,
	'minutes'   => 45,
	
	# récurrence de l'enregistrement.
	# 'repeat'    => array(1,2,3,4,5,6,7)

	# valeurs par défaut.
	# 'chaine'    => $chaine->id(),
	# 'service'	=> $chaine->services_id('.*auto.*'),
	# 'where_id'  => 2,       # disque dur local de la freebox

	# valeur sans fonction actuellement.
	# 'repeat_a'   => 1,
);

$enreg = new Enregistrement($chaine, $args);
echo "enregistrement : " . $enreg;

$magneto->programmer($enreg);
$liste = $magneto->liste_enregistrements();

foreach($liste as $enreg)
	echo $enreg;

?>
