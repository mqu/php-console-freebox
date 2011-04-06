<?php

# ce script permet de vérifier l'installation de votre environnement PHP.

function myphpinfo(){
	printf("# phpversion : %s\n", phpversion());
	printf("# uname : %s\n", php_uname());
}

function check_php_installation(){
	
	$functions_grp = array(
		'curl' => array(
			'package'     => 'CURL',
			'functions'   => array('curl_init'),
			'description' => 'communication avec des serveurs Web.',
		),
		'json' => array(
			'package'     => 'JSON',
			'functions'   => array('json_decode', 'json_encode', 'json_last_error'),
			'description' => 'encodage, decodage donnees Javascript',
		)
	);

	foreach($functions_grp as $grp){
		
		foreach($grp['functions'] as $f){
			if(!function_exists($f))
				throw new Exception(sprintf("fonction indisponible : package: %s / func: %s / php: %s / descr: %s", $grp['package'], $f, phpversion(), $grp['description']));
		}
	}
	
	printf("# verification : OK\n");
}

myphpinfo();
check_php_installation();


?>
