<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

$infos = $magneto->liste_enregistrements();

/*
 *        (
            [canal] => Array
                (
                    [0] => France 5
                    [1] => France 5 (bas d�bit)
                )

            [date] => 28/03/2011
            [heure] => 23h20
            [duree] => 3
            [nom] => test2
            [ide] => 92
            [chaine_id] => 5
            [service_id] => 2868
            [min] => 20
            [where_id] => 2
            [repeat_a] => 
*/

foreach($infos as $rec){
	printf("%s (%s)- %s %d:%d (%dmn) - %s\n", $rec['canal'][0], $rec['canal'][1], $rec['date'], $rec['h'], $rec['min'], $rec['dur'], $rec['name'] );
}

?>
