<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

require_once('Enregistrement.php');
require_once('EnregistrementFreebox.php');

$args = $_SERVER["argv"];

if($_SERVER["argc"] < 2){
	printf("usage : php -q %s id1 id2 ...(liste ID à supprimer)\n", $args[0]);
	printf("usage : php -q %s -1 (tout supprimer)\n", $args[0]);
	printf("usage : php -q %s 'expr' (expression régulière : .*test*)\n", $args[0]);
	exit (-1);
}
unset($args[0]);


$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

foreach($args as $id){

	# tout supprimer
	if($id == -1){
		$liste = $magneto->liste_enregistrements();
		foreach($liste as $enreg)
			$magneto->supprimer($enreg->ide);
		break;
	} elseif(!is_numeric($id))
		$magneto->supprimer_expr($id);
	else
		$magneto->supprimer($id);
}

foreach($magneto->lister() as $enreg)
	echo $enreg;


?>
