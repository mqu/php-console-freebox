<?php

require_once('Date.php');
require_once('Enregistrement.php');

class EnregistrementRecurrent extends Enregistrement{

	# protected $this->args['repeat'] = array();

	public function __construct(){
		parent::__construct();
		$this->args['repeat'] = array();
	}

	# répete l'évenement $count fois.
	# tient compte des récurrences sur la semaine ($this->repeat indique les jours d'occurence dans la semaine)
	# ne tient pas compte de $this->repeat si vide.
	public function repeat($count){
		$list = array();
		$tm = Date::date_to_tm($this->date);

		for ($i=0 ; $i<$count ; ){

			$dt = date('d/m/Y', $tm);
			
			# si repeat est vide ou si le jour de la semaine est dans la liste repeat
			if(in_array($this->week_day_number($dt), $this->repeat) || count($this->repeat)==0){
				$event = clone($this);
				$event->date = $dt;
				$list[] = $event;
				$i++;
			}
			$tm += 60*60*24;
		}
		
		return $list;
	}
	
	public function week_day_number($date = null){
		if($date == null)
			return Date::week_day_number(Date::date_to_tm($this->date));
		return Date::week_day_number(Date::date_to_tm($date));
	}
}

?>
