<?php

/* geany_encoding=ISO-8859-15 */

/*

	classe utilisée comme container lors de la liste des enregistrements de la freebox.

	forme :
	Array
	(

		# identifiants pour suppressions
		[form] => Array
			(
				[id] => form_list_119
				[method] => post
				[action] => ?id=208142&idt=2bdc99a98ae469dc
			)


		[date] => 08/04/2011
		[heure] => 23h30
		[min] => 30
		[duree] => 5


		[nom] => nom-emission

		# nom de la chaine.
		[canal] => Array
			(
				[0] => France 2
				[1] => France 2 (auto)
			)

		# identifiant de chaine, de canal
		[chaine_id] => 2
		[service_id] => 686

		# identifiant pour les suppressions.
		[ide] => 119

		
		[where_id] => 2  ; espace de stockage
		[repeat_a] =>    ; non utilisé actuellement.
	)
*/

class EnregistrementFreebox {
	protected $args;
	public function __construct($args){
		$this->args = $args;
	}

	public function __toString(){
		return sprintf("[%d] - %s %02d:%02d (%2dmn) - %s - \"%s\"", 
			$this->ide, 
			$this->date, 
			$this->heure, 
			$this->min, 
			$this->duree, 
			$this->canal, 
			$this->nom);
	}
	

    public function __set($name, $value) {
        $this->args[$name] = $value;
    }

    public function __get($name) {
		if($name == 'canal')
			return $this->args['canal'][0];
				
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
