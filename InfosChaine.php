<?php

/* geany_encoding=ISO-8859-15 */

# collection d'éléments de type InfosChaine
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
	
	public function __toString(){
		$txt = '';
		foreach($this->list as $info){
			$txt .= $info->__toString();
		}
		
		return $txt;
	}

	/*

		<select name="chaine">
		<option>TF1</option>
		<option selected="true">France 2</option>
		<option>France 3</option>
		</select>
	*/

	public function to_html_select($default=null, $name="chaine", $id="chaine"){
		$txt = "<select size='5' name='$name'\n";
		foreach($this->list as $info){
			$txt .= sprintf('<option value="%s">%s</option>' . "\n", $info->id(), $info->name());
		}
		$txt .= "</select>";
		return $txt;
	}
}

# class d'abstraction des informations liées aux chaines (id, name, service_id)
class InfosChaine {
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
	public function service_id($expr = '.*auto.*'){
		$list = array();
		foreach($this->info->service as $s){
			if(preg_match("#$expr#", $s->desc))
				return $s->id;
		}
		return $this->info->service[0]->id;
	}

	public function __toString(){

		$txt = sprintf("* %s [%d]\n", $this->name(), $this->id());
		
		foreach($this->info->service as $s){
			$txt .= sprintf("  - [%d] - %s\n", $s->id, $s->desc);
		}
		
		return $txt;
	}

}

?>
