<?php

/* geany_encoding=ISO-8859-15 */

class Date {

	static public function day($time=null){
			if($time == null)
					$time = time();
			return date('j', $time);
	}

	static public function month($time=null){
			if($time == null)
					$time = time();
			return date('n', $time);
	}

	static public function year($time=null){
			if($time == null)
					$time = time();
			return date('Y', $time);
	}

	static public function hour($time=null){
		if($time == null)
				$time = time();
		return date('H', $time);
	}

	static public function minute($time=null){
		if($time == null)
				$time = time();
		return date('i', $time);
	}

	static public function second($time=null){
		if($time == null)
				$time = time();
		return date('s', $time);
	}
	
	static public function today($format = 'd/m/Y'){
		return date($format);
	}

	# week day number sunday = 0, monday=1, ...
	static public function week_day_number($time=null){
			if($time == null)
					$time = time();
			return date('w', $time);
	}

	# week number from 1 to 52 (53)
	static public function week_number($time=null){
			if($time == null)
					$time = time();
			return date('W', $time);
	}

	# timestamp for this hour (minutes and seconds = 0)
	static public function this_hour(){
			$t = time();
			return $t - ($t % 3600);
	}

	# timestamp for this hour (H,M,S=0)
	static public function this_day(){
			return mktime(0,0,0);
	}

	# timestamp first day of week (monday)
	static public function this_week(){
			$t = self::this_day();
			$d = (self::week_day_number()-1)*3600*24;
			return $t - $d;
	}

	static public function this_month(){
			return mktime(0,0,0,self::month(),1);
	}

	static public function this_year(){
			return mktime(0,0,0,1,1);
	}

	static public function next_day(){
			return self::this_day() + 24*60*60;
	}

	static public function next_week(){
			return self::this_week() + 7 * 24*60*60;
	}

	static public function next_month(){
		return mktime(0,0,0,self::month()+1,1);
	}

	static public function next_year(){
		return mktime(0,0,0,1,1, self::year()+1);
	}

	# return monday timestamp for week $w
	static public function week_to_timestamp($w){
		$this_week = self::this_week();   # as timestamp
		$this_w    = self::week_number(); # as week number

		# number of seconds in a week
		$num_sec = 60 * 60 * 24 * 7;

		$t = ($w - $this_w) * $num_sec + $this_week;
		
		# décalage heure d'été
		if(Date::week_number($t) != $w)
				$t += 60*60;
		
		return $t;
	}
	static public function date_to_tm($date){
		list($d, $m, $y) = explode('/', $date);
		return mktime(0, 0, 0, $m, $d, $y);
	}
}

?>
