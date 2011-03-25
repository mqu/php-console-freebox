<?php

error_reporting(E_ALL);

require_once('config.php');
require_once('ConsoleFree.php');
require_once('ConsoleMagneto.php');

class InfoList {
	protected $list = array();
	
	public function __construct(){
	}
	
	public function add($info){
		$this->list[] = $info;
	}
	
	public function find_by_name($name='France 2'){
		foreach($this->list as $info){
			if($info->name() == $name)
				return $info;
		}
		foreach($this->list as $info){
			if(preg_match("#$name#", $info->name()))
				return $info;
		}
		return false;
	}
}
class InfoChaine {
	protected $info;
	
	public function __construct($info){
		$this->info = $info;
	}
	
	public function name(){
		return $this->info->name;
	}
	
	public function id(){
		return $this->info->id;
	}
	
	public function services_names(){
		$list = array();
		foreach($this->info->service as $s)
			$list[] = $s->desc;
		return $list;
	}
	
	# possible values : auto, standart, TNT, HD.*TNT, HD, bas.*d.*bit (bas débit)
	public function services_default_id($expr = '.*auto.*'){
		$list = array();
		foreach($this->info->service as $s){
			if(preg_match("#$expr#", $s->desc))
				return $s->id;
		}
		return $this->info->service[0]->id;
	}
}

$magneto = new ConsoleMagneto();
$magneto->login($config['user'], $config['passwd']);

if(file_exists('extra/info-chaine.dat')){
	$infos = unserialize(file_get_contents('extra/info-chaine.dat'));
}
else{
	$infos = $magneto->infos_chaines();
	file_put_contents('extra/info-chaine.dat', serialize($infos));
}

$list = new InfoList();

foreach($infos as $info){
	$list->add(new InfoChaine($info));
}

$chaine = $list->find_by_name('France 2');
$id = $chaine->services_default_id('.*auto.*');

print_r($chaine);
printf("id = $id\n");

?>
