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
		'emission'  => null
	);

	public function __construct(){
	}

	public function __toString(){
		return sprintf("%s - %s %d:%d (%dmn) - %s", 
			$this->chaine, 
			$this->date, 
			$this->heure, 
			$this->minutes, 
			$this->duree, 
			$this->emission);
	}

    public function __set($name, $value) {
        $this->args[$name] = $value;
        return $value;
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
