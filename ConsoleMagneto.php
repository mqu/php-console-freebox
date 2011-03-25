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

/*

            <div class="table block">
              <div class="content-title">LISTE DES ENREGISTREMENTS</div>
              <div class="tr">
                <table width="588" border="0" cellspacing="5" cellpadding="0" >
                  <!--header fixe-->
                  <tr>
                    <td><strong>CANAL</strong></td>
                    <td width="80"><strong>DATE</strong></td>
                    <td width="60"><strong>HEURE</strong></td>
                    <td width="60"><strong>DUREE</strong></td>
                    <td width="10"></td>
                    <td width="152"><strong>NOM</strong></td>
                    <td width="31"></td>
                    <td width="34"></td>
                  <tr>
                    <td height="1" colspan="8" bgcolor="#CCCCCC"></td>
                  </tr>
                  <!--//header fixe-->
		  
                
                  <tr>
                    <td colspan="8">&nbsp;</td>
                  </tr>
                  <form id="form_list_2" method="post" action="?id=208142&idt=6f323a94a52dd48b">
                  <tr >
                    <td><strong>France 2<br>France 2 (bas d�bit)</strong></td>
                    <td width="80" ><strong>07/03/2011</strong></td>
                    <td width="60" ><strong>20h00</strong></td>
                    <td width="60" ><strong>35</strong>mn</td>
                    <td width="10"><span title="Disque interne"><strong>(I)</strong></span></td>
                    <td width="152"><strong>france2-journal</strong></td>
		    <input type="hidden" name="ide"        value="2">
		    <input type="hidden" name="chaine_id"  value="2">
		    <input type="hidden" name="service_id" value="156">
		    <input type="hidden" name="date"       value="07/03/2011">
		    <input type="hidden" name="h"          value="20">
		    <input type="hidden" name="min"        value="00">
		    <input type="hidden" name="dur"        value="35">
		    <input type="hidden" name="name"       value="france2-journal">
		    <input type="hidden" name="where_id"   value="2">
		    <input type="hidden" name="repeat_a"   value="">
                    <td width="31">
	              
                    </td>
                    <td width="34">
                      <input type="submit" name="supp" value="Supprimer" alt="Supprimer" onClick="return confirm('Voulez vous vraiment supprimer cet enregistrement ?')"/>
                    </td>
                  </tr>

*/


	# url : https://adsl.free.fr/admin/magneto.pl?id=XXX&idt=YYYY&detail=0&box=0
	public function liste_enregistrements(){

		$url = sprintf('https://adsl.free.fr/admin/magneto.pl?id=%s&idt=%s&detail=0&box=%s', 
			$this->id(),
			$this->idt(),
			$this->opts['box']
		);

		$data = $this->curl->get($url);

		$html = new simple_html_dom($data);

		$list = $html->find('div[class=tr] table form');

		foreach($list as $elem){
			$list2 = $elem->find('input[type=hidden]');
			printf("element : %s\n", $this->html_trim($elem->plaintext));
			printf("  attr : %s\n", join(',', $elem->attr));
			echo "-\n";
			foreach($list2 as $elem2){
				printf("element : %s\n", $this->html_trim($elem2->plaintext));
				printf("  attr : %s\n", join(',', $elem2->attr));
			}
			echo "----\n\n";
		}
	}
	
/*
 * programmer :	
 * 
	chaine	3
	date	06/03/2011
	duree	5
	emission	test
	heure	23
	minutes	23
	service	161
	submit	PROGRAMMER L'ENREGISTREMENT
	where_id	2


	chaine	5
	date	07/03/2011
	duree	10
	emission	test
	heure	22
	minutes	13
	service	167
	submit	PROGRAMMER L'ENREGISTREMENT
	where_id	2


retour d'erreur : 
	    <div class="table block">
              <div class="content-title">Des erreurs sont survenues :</div>
              <div class="tr">
                <table width="584">
                  <tr>
                    <td width="85"><strong><span style="color: #cc0000">Date dans le passé</span></strong></td>
		         </tr>
		       </table>
	      </div>

        </div>

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
	# listes les BOXes : les boitiers multi-media ; il peut y avoir plusieurs boitier dans le meme domicile
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
	
	# url : https://adsl.free.fr/admin/magneto.pl?id=XXX&idt=YYYY&detail=1
	public function details(){

		return $this->details_url();
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
			$list = new InfoList();

			foreach($infos as $info){
				$list->add(new InfosChaine($info));
			}

			return $list;

		} else
			throw new Exception("Magneto : pas d'info sur les chaines");
	}
	
	# url : https://adsl.free.fr/admin/magneto.pl?id=XXX&idt=YYYY&detail=1
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
