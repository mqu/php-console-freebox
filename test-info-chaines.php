<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('ConsoleFree.php');
require_once('ConsoleMagneto.php');
require_once('InfosChaine.php');


$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

# mise en cache
if(file_exists('extra/info-chaine.dat')){
	$infos = unserialize(file_get_contents('extra/info-chaine.dat'));
}
else{
	$infos = $magneto->infos_chaines();
	file_put_contents('extra/info-chaine.dat', serialize($infos));
}

print_r($infos);

$list = new InfoList();

foreach($infos as $info){
	$list->add(new InfoChaine($info));
}

$chaine = $list->find_by_name('France 2');
$id = $chaine->services_default_id('.*auto.*');

print_r($chaine);
printf("id = $id\n");

?>
