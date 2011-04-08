<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('InfosChaine.php');
require_once('ConsoleMagneto.php');

class FreeboxEnregistrement {
	protected $attribs = array(
		'date'     => null,
		'heure'    => null,
		'minutes'  => null,
		'duree'    => null,

		'chaine'    => null,
		'service'   => null,

		'where_id'  => 2,
		'emission'  => null,
	);
	
	public function __construct($args = null){
		if($args != null){
			if(isset($args['date']))    $this->args['date']    = $args['date'];
			if(isset($args['heure']))   $this->args['heure']   = $args['heure'];
			if(isset($args['minutes'])) $this->args['minutes'] = $args['minutes'];
			if(isset($args['duree']))   $this->args['duree']   = $args['duree'];

			if(isset($args['chaine']))  $this->args['chaine']  = $args['chaine'];
			if(isset($args['service'])) $this->args['service'] = $args['service'];

			if(isset($args['where_id'])) $this->args['where_id'] = $args['where_id'];
			if(isset($args['emission'])) $this->args['emission'] = $args['emission'];
		}
	}
	
	public function __toString(){
		return sprintf("%s - %s %d:%d (%dmn) - %s\n", $this->args['chaine'], $this->args['date'], $this->args['heure'], $this->args['minutes'], $this->args['duree'], $this->args['emission']);
	}
}

$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

$infos = $magneto->infos_chaines();
$chaine = $infos->find_by_name('France 2');

# $date = '12/03/2011';
$date = date('d/m/Y');


$args = array(
	'chaine'    => $chaine->id(),
	'date'      => $date,
	'duree'	    => 5,
	'emission'	=> 'test',
	'heure'     => 23,
	'minutes'   => 30,
	'service'	=> $chaine->services_id('.*auto.*'),
	'where_id'  => 2,       # disque dur local de la freebox
	# 'repeat_a'   => 1,
);

$enreg = new FreeboxEnregistrement($args);
echo $enreg;

# $magneto->programmer($enreg);

?>
