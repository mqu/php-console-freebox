<?php

require_once('simplehtmldom.php');
require_once('InfosChaine.php');
require_once('ConsoleFree.php');

class ConsoleMagneto extends ConsoleFree{

	protected $opts = array(
		'box' => '0'
	);
	
	protected $data = array(
		'details' => null
	);

	public function __construct($opts = array()){
		parent::__construct();

		foreach($opts as $key=>$val){
			$this->opts[$key] = $val;
		}
	}

	# url : https://adsl.free.fr/admin/magneto.pl?id=XXX&idt=YYYY&detail=0&box=0
	/* retourne une liste d'enregistrements de cette forme :
	 * Array
		(
			[form] => Array
				(
					[id] => form_list_91
					[method] => post
					[action] => ?id=208142&idt=8a5f63b100394643
				)

			[canal] => Array
				(
					[0] => France 3
					[1] => France 3 (auto)
				)

			[date] => 28/03/2011
			[heure] => 22h11
			[duree] => 5
			[nom] => test1
			[ide] => 91
			[chaine_id] => 3
			[service_id] => 2863
			[min] => 11
			[where_id] => 2
			[repeat_a] => 
		)
	*/
	public function liste_enregistrements(){

		$url = sprintf('https://adsl.free.fr/admin/magneto.pl?id=%s&idt=%s&detail=0&box=%s', 
			$this->id(),
			$this->idt(),
			$this->opts['box']
		);

		$data = $this->curl->get($url);

		$html = new simple_html_dom($data);

		$list = $html->find('div[class=tr] table form');

		$list_infos = array();
		$names = array('canal', 'date', 'heure', 'duree', 'nom', 'nom');
		$names_unset = array('name', 'h', 'dur');

		foreach($list as $elem){

			$infos = array();
			$infos['form']['id'] = $elem->id;
			$infos['form']['method'] = $elem->method;
			$infos['form']['action'] = $elem->action;

			$infos1 = $elem->find('td strong');
			foreach($infos1 as $key=>$info)
				$infos[$names[$key]] = $info->innertext;
			
			$list2 = $elem->find('input[type=hidden]');

			foreach($list2 as $elem2){
				if(isset($elem2->attr['name']))
					$infos[$elem2->attr['name']] = $elem2->attr['value'];
			}
			
			$infos['canal'] = explode('<br>', $infos['canal']);
			$list_infos[] = $infos;
			
			# suppression des infos en double
			foreach($names_unset as $k)
				unset($infos[$k]);
				
			print_r($infos);
		}
		return $list_infos;
	}
	
/*
 * programmer :	permet de programmer un enregistrement
 * 
	* spécifications temporelles : date, durée, heure, minutes
	* emission : nom du fichier d'enregistrement,
	* service : identifiant numérique spécifiant le canal de diffusion ; valeur possible $info->chaine('France 2')->services_default_id('.*auto.*')
	* chaine : identificant de la chaine ; valeur possible $info->chaine('France 2')->id()
	* where_id : identifiant du media-player (espace de stockage disque)
	* champs supplémentaire supposé : 
	*   - repeat_a (liste des jours de la semaine ou l'enregistrement doit avoir lieu
	*   - sur Freebox V5 : <input type="checkbox" name="period" value="1" id="period_1" -> Lundi

	* 
*/

	public function programmer($args=array()){
		if(!isset($args['submit']))
			$args['submit'] = "PROGRAMMER L'ENREGISTREMENT";
			
		if(!isset($args['where_id']))  # disque dur par défaut (internal-disk : "/Disque dur/Enregistrements")
			$args['where_id'] = 2;
			
		$url = sprintf('https://adsl.free.fr/admin/magneto.pl?id=%s&idt=%s', 
			$this->id(),
			$this->idt());
		$data = $this->curl->post($url, $args);
		if(preg_match('#Des erreurs sont survenues :#', $data)){
			$html = new simple_html_dom($data);
			$list = $html->find('div div[class=tr] strong span[style=]');
			$names = array();
			foreach($list as $elem){
				$names[] = utf8_decode($elem->plaintext);
			}
			$error = utf8_decode(join(' - ', $names));
			throw new Exception ("erreur de programmation : $error");
		}
	}
	
	# url : https://adsl.free.fr/admin/magneto.pl?id=XXX&idt=YYYY&detail=0&box=0
	# listes les BOXes : les boitiers multi-media ; il peut y avoir plusieurs boitiers dans le même domicile
	public function liste_boxes(){

		$url = sprintf('https://adsl.free.fr/admin/magneto.pl?id=%s&idt=%s&detail=0&box=%s', 
			$this->id(),
			$this->idt(),
			$this->opts['box']
		);

		$data = $this->curl->get($url);

		$html = new simple_html_dom($data);

		# <div  id="content" class="television"> / <div class="block-container"> / <a href=...>
		$boxes = array();

		$list = $html->find('div[id=content] div[class=block-container] a');
		foreach($list as $elem){
			$name = utf8_decode($this->html_trim($elem->plaintext));
			$attribs = join(',', $elem->attr);
			if(preg_match('#box=(\d+)$#', $attribs, $values))
				$attribs = $values[1];
			else
				$attribs = null;
				
			$boxes[$name] = $attribs;
		}
		return $boxes;
	}	
	

	# retourne un enregistrement contenant des infos sur les disques et espaces disponibles
	#
	public function infos_disks(){
		# var disk_a = [{"free_size":195334180,"total_size":239347164,"label":"internal-disk","id":2,"mount_point":"/Disque dur/Enregistrements"}];
		if(preg_match('#var disk_a = \[(.*)\];#', $this->details_url(), $values)){
			return json_decode($values[1]);
		} else
			throw new Exception("Magneto : pas d'info sur l'espace disque");
	}
	
	# listes des chaines supportées, id
	public function infos_chaines(){
		# var serv_a = [{"name":"TF1","id":1,"service":[{"pvr_mode":"public","desc":"TF1 (TNT)","id":847},{"pvr_mode":"private","desc":"TF1 (HD)","id":150},...
		if(preg_match('#var serv_a = (.*);#', $this->details_url(), $values)){
			$infos = json_decode(utf8_decode($values[1]));
			if($infos === NULL)
				throw new Exception(sprintf("Magneto : erreur décodage infos chaines (%d)", json_last_error()));
				
			# print_r($infos);
			
			$list = new InfoList();

			foreach($infos as $info){
				$list->add(new InfosChaine($info));
			}

			return $list;

		} else
			throw new Exception("Magneto : pas d'info sur les chaines");
	}
	
	# url : https://adsl.free.fr/admin/magneto.pl?id=XXX&idt=YYYY&detail=1
	# retourne en format JSON, la listes des chaines, des ID de diffusion par qualité d'enregistrement
	# un cache permet d'éviter de récupérer en double sur le serveur
	private function details_url(){

		if($this->data['details'] == null){
			$url = sprintf('https://adsl.free.fr/admin/magneto.pl?id=%s&idt=%s&detail=1', 
				$this->id(),
				$this->idt()
			);

			$this->data['details'] = $this->curl->get($url);
		}
		
		return $this->data['details'];
	}

	public function supprimer(){
	}

	function html_trim($str){
		$str = html_entity_decode($str, ENT_COMPAT, 'ISO-8859-15');
		$str = preg_replace('#[\xA0 \n\r\t]+#', ' ', $str);

		return trim($str);
	}

}

?>
