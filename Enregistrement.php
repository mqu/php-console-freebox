<?php

class Enregistrement {
	protected $args = array(
		'date'     => null,
		'heure'    => null,
		'minutes'  => null,
		'duree'    => null,

		'chaine'    => null,
		'service'   => null,

		'where_id'  => 2,
		'emission'  => null,
		
		'repeat'   => array()
	);
	
	protected $chaine;
	
	public function __construct($chaine, $args = null){
		$this->chaine = $chaine;
		if($args != null){
			if(isset($args['date']))    $this->args['date']    = $args['date'];
			if(isset($args['heure']))   $this->args['heure']   = $args['heure'];
			if(isset($args['minutes'])) $this->args['minutes'] = $args['minutes'];
			if(isset($args['duree']))   $this->args['duree']   = $args['duree'];

			if(isset($args['chaine']))  $this->args['chaine']  = $args['chaine'];
			if(isset($args['service'])) $this->args['service'] = $args['service'];

			if(isset($args['where_id'])) $this->args['where_id'] = $args['where_id'];
			if(isset($args['emission'])) $this->args['emission'] = $args['emission'];
			if(isset($args['repeat']))   $this->args['repeat']   = $args['repeat'];
		}
		
		if($this->args['service'] == null)
			$this->args['service'] = $this->chaine->services_id('.*auto.*');

		if($this->args['chaine'] == null)
			$this->args['chaine'] = $this->chaine->id();
	}
	
	public function __toString(){
		return sprintf("%s - %s %d:%d (%dmn) - %s (%s)\n", 
			$this->args['chaine'], 
			$this->args['date'], 
			$this->args['heure'], 
			$this->args['minutes'], 
			$this->args['duree'], 
			$this->args['emission'],
			join(', ', $this->args['repeat']));
	}
	
    public function __set($name, $value) {
        $this->args[$name] = $value;
    }

    public function __get($name) {
        if (array_key_exists($name, $this->args)) {
            return $this->args[$name];
        }

        $trace = debug_backtrace();
        trigger_error(
            'Undefined property via __get(): ' . $name .
            ' in ' . $trace[0]['file'] .
            ' on line ' . $trace[0]['line'],
            E_USER_NOTICE);
        return null;
    }
}

?>
