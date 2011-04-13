<?php

require_once('ConsoleMagneto.php');

class Page {
	protected $params = array(
	  'actions' => array("login","lister", "programmer", "supprimer"),
	);
	protected $magneto;

	public function __construct(){
		session_start();
		
	}

	protected function header(){

		header("Content-Type: text/html; charset=UTF-8");
	
		echo <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>enregistrement freebox</title>
	<script type="text/javascript" src="js/jquery-mini.js"></script>
	<script type="text/javascript" src="js/magneto.js"></script>
</head>
<body>

END;

	}
	
	protected function footer(){
		echo "</body></html>\n";
	}

	public function run(){

		$this->header();

		$this->menu();

		try{
			$action = $this->get_arg('action', false);
			
			if($action == false)
				return false;

			if($action != 'login' && isset($_SESSION['id'])){
				$this->magneto = new ConsoleMagneto();
				$this->magneto->set_from_session($_SESSION['id'], $_SESSION['idt']);
			}

			$act = sprintf('$this->run_%s();', $action);
			eval($act);
		}

		catch(SessionException $e){
			printf("une erreur s'est produite : déconnexion (session timeout) ; vous devez vous reconnecter ...<br>\n", $e->getMessage());
			unset($_SESSION['id']);
			unset($_SESSION['idt']);
			$this->location('?action=login');
		}
		catch(Exception $e){
			printf("une erreur s'est produite : %s<br>\n", $e->getMessage());
		}

		$this->footer();
	}
	
	protected function login_form(){
		
		$login = $this->get_arg('login', '');
		echo <<<END
		
Connexion : 
<form method="post" action="?action=login">
<input type="text" name="login" value="$login"> Login<br>
<input type="password" name="passwd"> Password<br>
<input type="submit" value="valider">
</form>

END;
	}

	protected function programmer_form(){
		
		$args = array('chaine', 'qualite', 'duree', 'emission', 'repeat');
		foreach($args as $v)
			$a[$v] = $this->get_arg($v, '');

		$a['date'] = $this->get_arg($v, date('d/m/Y'));
		$a['heure'] = $this->get_arg($v, date('h'));
		# $a['minutes'] = 5+$this->get_arg($v, date('i'));
		$a['minutes'] = 5;

		echo <<<END


<script>

var serv_a = [{"name":"TF1","id":1,"service":[{"pvr_mode":"private","desc":"TF1 (HD)","id":679},{"pvr_mode":"private","desc":"TF1 (standard)","id":680},{"pvr_mode":"private","desc":"TF1 (bas débit)","id":681},{"pvr_mode":"private","desc":"TF1 (auto)","id":682},{"pvr_mode":"public","desc":"TF1 (TNT)","id":1230},{"pvr_mode":"public","desc":"TF1 HD (TNT)","id":1237}]},{"name":"France 2","id":2,"service":[{"pvr_mode":"public","desc":"France 2 (auto)","id":686},{"pvr_mode":"public","desc":"France 2 (TNT)","id":1240},{"pvr_mode":"public","desc":"France 2 (HD)","id":683},{"pvr_mode":"public","desc":"France 2 (standard)","id":684},{"pvr_mode":"public","desc":"France 2 (bas débit)","id":685},{"pvr_mode":"public","desc":"France 2 HD (TNT)","id":1238}]},{"name":"France 3","id":3,"service":[{"pvr_mode":"public","desc":"France 3 (HD)","id":687},{"pvr_mode":"public","desc":"France 3 (standard)","id":688},{"pvr_mode":"public","desc":"France 3 (bas débit)","id":689},{"pvr_mode":"public","desc":"France 3 (auto)","id":690},{"pvr_mode":"public","desc":"France3 (TNT)","id":1244}]},{"name":"Canal+","id":4,"service":[{"pvr_mode":"private","desc":"Canal+ (auto)","id":693},{"pvr_mode":"private","desc":"Canal+ (HD)","id":691},{"pvr_mode":"private","desc":"Canal+ (standard)","id":692},{"pvr_mode":"public","desc":"CANAL+ (TNT)","id":1225}]},{"name":"France 5","id":5,"service":[{"pvr_mode":"public","desc":"France 5 (auto)","id":696},{"pvr_mode":"public","desc":"France 5 (standard)","id":694},{"pvr_mode":"public","desc":"France 5 (bas débit)","id":695},{"pvr_mode":"public","desc":"France 5 (TNT)","id":1241}]},{"name":"M6","id":6,"service":[{"pvr_mode":"private","desc":"M6 (auto)","id":700},{"pvr_mode":"public","desc":"M6 (TNT)","id":1214},{"pvr_mode":"private","desc":"M6 (HD)","id":697},{"pvr_mode":"private","desc":"M6 (standard)","id":698},{"pvr_mode":"private","desc":"M6 (bas débit)","id":699},{"pvr_mode":"public","desc":"M6HD (TNT)","id":1239}]},{"name":"Arte","id":7,"service":[{"pvr_mode":"public","desc":"ARTE HD (TNT)","id":1218},{"pvr_mode":"public","desc":"Arte (HD)","id":701},{"pvr_mode":"public","desc":"Arte (standard)","id":702},{"pvr_mode":"public","desc":"Arte (bas débit)","id":703},{"pvr_mode":"public","desc":"Arte (auto)","id":704},{"pvr_mode":"public","desc":"ARTE (TNT)","id":1236}]},{"name":"Direct 8","id":8,"service":[{"pvr_mode":"public","desc":"Direct 8 (bas débit)","id":707},{"pvr_mode":"public","desc":"Direct 8 (auto)","id":708},{"pvr_mode":"public","desc":"Direct 8 (HD)","id":705},{"pvr_mode":"public","desc":"Direct 8 (standard)","id":706},{"pvr_mode":"public","desc":"Direct 8 (TNT)","id":1219}]},{"name":"W9","id":9,"service":[{"pvr_mode":"private","desc":"W9 (auto)","id":711},{"pvr_mode":"private","desc":"W9 (standard)","id":709},{"pvr_mode":"private","desc":"W9 (bas débit)","id":710},{"pvr_mode":"public","desc":"W9 (TNT)","id":1215}]},{"name":"TMC","id":10,"service":[{"pvr_mode":"public","desc":"TMC (auto)","id":714},{"pvr_mode":"public","desc":"TMC (standard)","id":712},{"pvr_mode":"public","desc":"TMC (bas débit)","id":713},{"pvr_mode":"public","desc":"TMC (TNT)","id":1235}]},{"name":"NT1","id":11,"service":[{"pvr_mode":"public","desc":"NT1 (auto)","id":717},{"pvr_mode":"public","desc":"NT1 (standard)","id":715},{"pvr_mode":"public","desc":"NT1 (bas débit)","id":716},{"pvr_mode":"public","desc":"NT1 (TNT)","id":1216}]},{"name":"NRJ 12","id":12,"service":[{"pvr_mode":"public","desc":"NRJ 12 (bas débit)","id":721},{"pvr_mode":"public","desc":"NRJ 12 (auto)","id":722},{"pvr_mode":"public","desc":"NRJ 12 (HD)","id":718},{"pvr_mode":"private","desc":"NRJ 12 (3D)","id":719},{"pvr_mode":"public","desc":"NRJ 12 (standard)","id":720},{"pvr_mode":"public","desc":"NRJ12 (TNT)","id":1231}]},{"name":"La Chaîne Parlementaire","id":13,"service":[{"pvr_mode":"public","desc":"La Chaîne Parlementaire (standard)","id":723},{"pvr_mode":"public","desc":"La Chaîne Parlementaire (bas débit)","id":724},{"pvr_mode":"public","desc":"La Chaîne Parlementaire (auto)","id":725},{"pvr_mode":"public","desc":"LCP (TNT)","id":1243}]},{"name":"France 4","id":14,"service":[{"pvr_mode":"public","desc":"France 4 (standard)","id":726},{"pvr_mode":"public","desc":"France 4 (bas débit)","id":727},{"pvr_mode":"public","desc":"France 4 (auto)","id":728},{"pvr_mode":"public","desc":"France 4 (TNT)","id":1224}]},{"name":"BFM TV","id":15,"service":[{"pvr_mode":"public","desc":"BFM TV (HD)","id":729},{"pvr_mode":"public","desc":"BFM TV (standard)","id":730},{"pvr_mode":"public","desc":"BFM TV (bas débit)","id":731},{"pvr_mode":"public","desc":"BFM TV (auto)","id":732},{"pvr_mode":"public","desc":"BFM TV (TNT)","id":1220}]},{"name":"i> TELE","id":16,"service":[{"pvr_mode":"public","desc":"i> TELE (auto)","id":735},{"pvr_mode":"public","desc":"i> TELE (standard)","id":733},{"pvr_mode":"public","desc":"i> TELE (bas débit)","id":734},{"pvr_mode":"public","desc":"i>TELE (TNT)","id":1221}]},{"name":"DirectStar","id":17,"service":[{"pvr_mode":"public","desc":"DirectStar (auto)","id":738},{"pvr_mode":"public","desc":"DirectStar (standard)","id":736},{"pvr_mode":"public","desc":"DirectStar (bas débit)","id":737},{"pvr_mode":"public","desc":"DirectStar (TNT)","id":1222}]},{"name":"Gulli","id":18,"service":[{"pvr_mode":"private","desc":"Gulli (auto)","id":741},{"pvr_mode":"private","desc":"Gulli (standard)","id":739},{"pvr_mode":"private","desc":"Gulli (bas débit)","id":740},{"pvr_mode":"public","desc":"Gulli (TNT)","id":1223}]},{"name":"RTL9","id":21,"service":[{"pvr_mode":"public","desc":"RTL9 (auto)","id":750},{"pvr_mode":"public","desc":"RTL9 (bas débit)","id":748},{"pvr_mode":"public","desc":"RTL9 (standard)","id":749}]},{"name":"AB 1","id":22,"service":[{"pvr_mode":"public","desc":"AB 1 (auto)","id":753},{"pvr_mode":"public","desc":"AB 1 (bas débit)","id":751},{"pvr_mode":"public","desc":"AB 1 (standard)","id":752}]},{"name":"Disney Channel","id":23,"service":[{"pvr_mode":"private","desc":"Disney Channel (bas débit)","id":754}]},{"name":"TV5 Monde","id":25,"service":[{"pvr_mode":"public","desc":"TV5 Monde (standard)","id":756}]},{"name":"FHV Home Video","id":33,"service":[{"pvr_mode":"private","desc":"FHV Home Video (auto)","id":765},{"pvr_mode":"private","desc":"FHV Home Video (standard)","id":763},{"pvr_mode":"private","desc":"FHV Home Video (bas débit)","id":764}]},{"name":"C+ Cinema","id":41,"service":[{"pvr_mode":"private","desc":"C+ Cinema (auto)","id":783},{"pvr_mode":"private","desc":"C+ Cinema (HD)","id":781},{"pvr_mode":"private","desc":"C+ Cinema (standard)","id":782},{"pvr_mode":"public","desc":"CANAL+ CINEMA (TNT)","id":1226}]},{"name":"C+ Sport","id":42,"service":[{"pvr_mode":"private","desc":"C+ Sport (auto)","id":786},{"pvr_mode":"private","desc":"C+ Sport (HD)","id":784},{"pvr_mode":"private","desc":"C+ Sport (standard)","id":785},{"pvr_mode":"public","desc":"CANAL+ SPORT (TNT)","id":1227}]},{"name":"C+ Decale","id":43,"service":[{"pvr_mode":"private","desc":"C+ Decale (auto)","id":789},{"pvr_mode":"private","desc":"C+ Decale (HD)","id":787},{"pvr_mode":"private","desc":"C+ Decale (standard)","id":788}]},{"name":"C+ Family","id":44,"service":[{"pvr_mode":"private","desc":"C+ Family (auto)","id":792},{"pvr_mode":"private","desc":"C+ Family (HD)","id":790},{"pvr_mode":"private","desc":"C+ Family (standard)","id":791}]},{"name":"Vivolta","id":47,"service":[{"pvr_mode":"public","desc":"Vivolta (bas débit)","id":796}]},{"name":"NRJ Hits","id":59,"service":[{"pvr_mode":"public","desc":"NRJ Hits (standard)","id":807},{"pvr_mode":"public","desc":"NRJ Hits (bas débit)","id":808},{"pvr_mode":"public","desc":"NRJ Hits (HD)","id":806},{"pvr_mode":"public","desc":"NRJ Hits (auto)","id":809}]},{"name":"Télé Mélody","id":67,"service":[{"pvr_mode":"private","desc":"Télé Mélody (standard)","id":824}]},{"name":"Mezzo","id":68,"service":[{"pvr_mode":"private","desc":"Mezzo (standard)","id":825}]},{"name":"Clubbing TV","id":72,"service":[{"pvr_mode":"public","desc":"Clubbing TV (standard)","id":831},{"pvr_mode":"public","desc":"Clubbing TV (HD)","id":829},{"pvr_mode":"public","desc":"Clubbing TV (bas débit)","id":830},{"pvr_mode":"public","desc":"Clubbing TV (auto)","id":832}]},{"name":"Avis à la population","id":74,"service":[{"pvr_mode":"public","desc":"Avis à la population (bas débit)","id":834}]},{"name":"BeBlack","id":78,"service":[{"pvr_mode":"public","desc":"BeBlack (bas débit)","id":837}]},{"name":"OFIVE","id":79,"service":[{"pvr_mode":"public","desc":"OFIVE (bas débit)","id":838}]},{"name":"BFM Business","id":80,"service":[{"pvr_mode":"public","desc":"BFM Business (bas débit)","id":839}]},{"name":"Euronews","id":82,"service":[{"pvr_mode":"public","desc":"Euronews (standard)","id":840}]},{"name":"Bloomberg TV","id":83,"service":[{"pvr_mode":"public","desc":"Bloomberg TV (standard)","id":841}]},{"name":"Al Jazeera International","id":85,"service":[{"pvr_mode":"public","desc":"Al Jazeera International (standard)","id":843}]},{"name":"Sky News International","id":87,"service":[{"pvr_mode":"public","desc":"Sky News International (standard)","id":845}]},{"name":"Guysen TV","id":88,"service":[{"pvr_mode":"public","desc":"Guysen TV (standard)","id":846}]},{"name":"CNBC","id":89,"service":[{"pvr_mode":"public","desc":"CNBC (standard)","id":847}]},{"name":"LCP - Assemblée Nationale 24h/24","id":90,"service":[{"pvr_mode":"public","desc":"LCP - Assemblée Nationale 24h/24 (standard)","id":848}]},{"name":"Public Sénat","id":91,"service":[{"pvr_mode":"public","desc":"Public Sénat (standard)","id":849}]},{"name":"Grand Lille TV","id":92,"service":[{"pvr_mode":"public","desc":"Grand Lille TV (standard)","id":850}]},{"name":"Ma Chaîne Etudiante","id":93,"service":[{"pvr_mode":"public","desc":"Ma Chaîne Etudiante (standard)","id":851}]},{"name":"France 24","id":95,"service":[{"pvr_mode":"public","desc":"France 24 (standard)","id":853}]},{"name":"France 24 English","id":96,"service":[{"pvr_mode":"public","desc":"France 24 English (standard)","id":854}]},{"name":"France 24 Arab","id":97,"service":[{"pvr_mode":"public","desc":"France 24 Arab (standard)","id":855}]},{"name":"Cinéma(s) à la demande 3D","id":110,"service":[{"pvr_mode":"private","desc":"Cinéma(s) à la demande 3D (bas débit)","id":857}]},{"name":"Game One","id":118,"service":[{"pvr_mode":"private","desc":"Game One (standard)","id":865}]},{"name":"Game One Music HD","id":119,"service":[{"pvr_mode":"public","desc":"Game One Music HD (HD)","id":866}]},{"name":"KidsCo","id":120,"service":[{"pvr_mode":"private","desc":"KidsCo (standard)","id":867}]},{"name":"Lucky Jack","id":121,"service":[{"pvr_mode":"public","desc":"Lucky Jack (auto)","id":870},{"pvr_mode":"public","desc":"Lucky Jack (HD)","id":868},{"pvr_mode":"public","desc":"Lucky Jack (bas débit)","id":869}]},{"name":"Men's Up TV","id":122,"service":[{"pvr_mode":"public","desc":"Men's Up TV (bas débit)","id":871}]},{"name":"Nolife","id":123,"service":[{"pvr_mode":"public","desc":"Nolife (standard)","id":872}]},{"name":"Fashion TV","id":131,"service":[{"pvr_mode":"public","desc":"Fashion TV (standard)","id":881}]},{"name":"World Fashion","id":132,"service":[{"pvr_mode":"public","desc":"World Fashion (standard)","id":882}]},{"name":"Souvenirs from Earth","id":133,"service":[{"pvr_mode":"public","desc":"Souvenirs from Earth (standard)","id":883}]},{"name":"Renault TV","id":139,"service":[{"pvr_mode":"public","desc":"Renault TV (bas débit)","id":890}]},{"name":"Equidia","id":141,"service":[{"pvr_mode":"private","desc":"Equidia (standard)","id":892}]},{"name":"AB Moteurs","id":143,"service":[{"pvr_mode":"public","desc":"AB Moteurs (bas débit)","id":894},{"pvr_mode":"public","desc":"AB Moteurs (standard)","id":895},{"pvr_mode":"public","desc":"AB Moteurs (auto)","id":896}]},{"name":"Poker Channel","id":145,"service":[{"pvr_mode":"public","desc":"Poker Channel (standard)","id":900}]},{"name":"Onzeo","id":150,"service":[{"pvr_mode":"public","desc":"Onzeo (bas débit)","id":907}]},{"name":"France ô","id":152,"service":[{"pvr_mode":"public","desc":"France ô (standard)","id":909},{"pvr_mode":"public","desc":"France Ô (TNT)","id":1242}]},{"name":"Liberty TV","id":154,"service":[{"pvr_mode":"public","desc":"Liberty TV (standard)","id":911}]},{"name":"Montagne TV","id":156,"service":[{"pvr_mode":"public","desc":"Montagne TV (bas débit)","id":915}]},{"name":"Luxe.TV","id":157,"service":[{"pvr_mode":"public","desc":"Luxe.TV (auto)","id":918},{"pvr_mode":"public","desc":"Luxe.TV (HD)","id":916},{"pvr_mode":"public","desc":"Luxe.TV (standard)","id":917}]},{"name":"Demain.tv","id":163,"service":[{"pvr_mode":"public","desc":"Demain.tv (standard)","id":932}]},{"name":"KTO","id":164,"service":[{"pvr_mode":"public","desc":"KTO (standard)","id":933}]},{"name":"MyZen.tv","id":165,"service":[{"pvr_mode":"private","desc":"MyZen.tv (standard)","id":936},{"pvr_mode":"private","desc":"MyZen.tv (HD)","id":934},{"pvr_mode":"private","desc":"MyZen.tv (3D)","id":935},{"pvr_mode":"private","desc":"MyZen.tv (auto)","id":937}]},{"name":"Wild Earth 3D","id":166,"service":[{"pvr_mode":"public","desc":"Wild Earth 3D (3D)","id":938}]},{"name":"TNA","id":168,"service":[{"pvr_mode":"public","desc":"TNA (standard)","id":940}]},{"name":"Freenews TV","id":169,"service":[{"pvr_mode":"public","desc":"Freenews TV (standard)","id":941}]},{"name":"Penthouse HD","id":176,"service":[{"pvr_mode":"private","desc":"Penthouse HD (HD)","id":943}]},{"name":"Pink TV","id":188,"service":[{"pvr_mode":"private","desc":"Pink TV (standard)","id":956}]},{"name":"M6 Boutique & Co","id":191,"service":[{"pvr_mode":"private","desc":"M6 Boutique & Co (auto)","id":960},{"pvr_mode":"private","desc":"M6 Boutique & Co (bas débit)","id":958},{"pvr_mode":"public","desc":"M6 Boutique & Co (standard)","id":959}]},{"name":"Terre d'infos","id":192,"service":[{"pvr_mode":"public","desc":"Terre d'infos (bas débit)","id":961}]},{"name":"Best of Shopping","id":193,"service":[{"pvr_mode":"public","desc":"Best of Shopping (standard)","id":963},{"pvr_mode":"private","desc":"Best of Shopping (auto)","id":964},{"pvr_mode":"private","desc":"Best of Shopping (bas débit)","id":962}]},{"name":"Astro Center TV","id":195,"service":[{"pvr_mode":"public","desc":"Astro Center TV (standard)","id":965}]},{"name":"Cash TV","id":196,"service":[{"pvr_mode":"public","desc":"Cash TV (standard)","id":966}]},{"name":"TLM","id":200,"service":[{"pvr_mode":"public","desc":"TLM (standard)","id":967}]},{"name":"TLT - Toulouse","id":201,"service":[{"pvr_mode":"public","desc":"TLT - Toulouse (standard)","id":968}]},{"name":"TV7 Bordeaux","id":202,"service":[{"pvr_mode":"public","desc":"TV7 Bordeaux (standard)","id":969}]},{"name":"TV8 Mont-Blanc","id":203,"service":[{"pvr_mode":"public","desc":"TV8 Mont-Blanc (standard)","id":970}]},{"name":"TéléGrenoble","id":204,"service":[{"pvr_mode":"public","desc":"TéléGrenoble (standard)","id":971}]},{"name":"Telif","id":205,"service":[{"pvr_mode":"public","desc":"Telif (standard)","id":972}]},{"name":"La Locale","id":206,"service":[{"pvr_mode":"public","desc":"La Locale (standard)","id":973}]},{"name":"Normandie TV","id":207,"service":[{"pvr_mode":"public","desc":"Normandie TV (standard)","id":974}]},{"name":"Télénantes Nantes 7","id":208,"service":[{"pvr_mode":"public","desc":"Télénantes Nantes 7 (standard)","id":975}]},{"name":"La Chaîne Marseille","id":209,"service":[{"pvr_mode":"public","desc":"La Chaîne Marseille (standard)","id":976}]},{"name":"Clermont Première","id":210,"service":[{"pvr_mode":"public","desc":"Clermont Première (standard)","id":977}]},{"name":"TV Tours","id":211,"service":[{"pvr_mode":"public","desc":"TV Tours (standard)","id":978}]},{"name":"NRJ Paris","id":212,"service":[{"pvr_mode":"public","desc":"NRJ Paris (auto)","id":981},{"pvr_mode":"public","desc":"NRJ Paris (standard)","id":979},{"pvr_mode":"public","desc":"NRJ Paris (bas débit)","id":980}]},{"name":"BFM Business Paris","id":213,"service":[{"pvr_mode":"public","desc":"BFM Business Paris (standard)","id":982}]},{"name":"IDF 1","id":214,"service":[{"pvr_mode":"public","desc":"IDF 1 (standard)","id":983}]},{"name":"Locales Ile de France","id":215,"service":[{"pvr_mode":"public","desc":"Locales Ile de France (standard)","id":984}]},{"name":"Alsace 20","id":216,"service":[{"pvr_mode":"public","desc":"Alsace 20 (standard)","id":985}]},{"name":"Telessonne","id":217,"service":[{"pvr_mode":"public","desc":"Telessonne (standard)","id":986}]},{"name":"TV Fil 78","id":218,"service":[{"pvr_mode":"public","desc":"TV Fil 78 (bas débit)","id":987}]},{"name":"Wéo","id":219,"service":[{"pvr_mode":"public","desc":"Wéo (standard)","id":988}]},{"name":"Canal 10 Guadeloupe","id":222,"service":[{"pvr_mode":"public","desc":"Canal 10 Guadeloupe (bas débit)","id":989}]},{"name":"Yvelines Première","id":223,"service":[{"pvr_mode":"public","desc":"Yvelines Première (bas débit)","id":990}]},{"name":"Calaisis TV","id":224,"service":[{"pvr_mode":"public","desc":"Calaisis TV (bas débit)","id":991}]},{"name":"TV SUD Camargue Cévennes","id":225,"service":[{"pvr_mode":"public","desc":"TV SUD Camargue Cévennes (bas débit)","id":992}]},{"name":"Mirabelle TV","id":226,"service":[{"pvr_mode":"public","desc":"Mirabelle TV (bas débit)","id":993}]},{"name":"Vosges Television","id":227,"service":[{"pvr_mode":"public","desc":"Vosges Television (bas débit)","id":994}]},{"name":"TV SUD Montpellier","id":228,"service":[{"pvr_mode":"public","desc":"TV SUD Montpellier (bas débit)","id":995}]},{"name":"Vox Africa","id":286,"service":[{"pvr_mode":"public","desc":"Vox Africa (standard)","id":1014}]},{"name":"Canal Info News","id":287,"service":[{"pvr_mode":"public","desc":"Canal Info News (standard)","id":1015}]},{"name":"MBOA TV","id":291,"service":[{"pvr_mode":"public","desc":"MBOA TV (bas débit)","id":1018}]},{"name":"Mosaïque F3","id":325,"service":[{"pvr_mode":"public","desc":"Mosaïque F3 (standard)","id":1025}]},{"name":"Nesma","id":479,"service":[{"pvr_mode":"public","desc":"Nesma (bas débit)","id":1026}]},{"name":"Medi 1 sat","id":480,"service":[{"pvr_mode":"public","desc":"Medi 1 sat (standard)","id":1027}]},{"name":"Canal Algérie","id":481,"service":[{"pvr_mode":"public","desc":"Canal Algérie (standard)","id":1028}]},{"name":"Algérie 3","id":482,"service":[{"pvr_mode":"public","desc":"Algérie 3 (bas débit)","id":1029}]},{"name":"Algérie 5","id":483,"service":[{"pvr_mode":"public","desc":"Algérie 5 (bas débit)","id":1030}]},{"name":"Tamazight TV4","id":484,"service":[{"pvr_mode":"public","desc":"Tamazight TV4 (bas débit)","id":1031}]},{"name":"Beur TV","id":486,"service":[{"pvr_mode":"public","desc":"Beur TV (standard)","id":1032}]},{"name":"CCTV4","id":500,"service":[{"pvr_mode":"private","desc":"CCTV4 (standard)","id":1036}]},{"name":"CCTV 9","id":501,"service":[{"pvr_mode":"public","desc":"CCTV 9 (standard)","id":1037}]},{"name":"CCTV F","id":502,"service":[{"pvr_mode":"public","desc":"CCTV F (standard)","id":1038}]},{"name":"CCTV Divertissement","id":503,"service":[{"pvr_mode":"private","desc":"CCTV Divertissement (standard)","id":1039}]},{"name":"La chaîne chinoise","id":504,"service":[{"pvr_mode":"private","desc":"La chaîne chinoise (standard)","id":1040}]},{"name":"Beijing TV","id":505,"service":[{"pvr_mode":"private","desc":"Beijing TV (standard)","id":1041}]},{"name":"Shanghai Dragon TV","id":506,"service":[{"pvr_mode":"private","desc":"Shanghai Dragon TV (standard)","id":1042}]},{"name":"La chaîne internationale de Jiangsu","id":507,"service":[{"pvr_mode":"private","desc":"La chaîne internationale de Jiangsu (standard)","id":1043}]},{"name":"Hunan Satellite TV","id":508,"service":[{"pvr_mode":"private","desc":"Hunan Satellite TV (standard)","id":1044}]},{"name":"Xiamen Star TV","id":509,"service":[{"pvr_mode":"private","desc":"Xiamen Star TV (standard)","id":1045}]},{"name":"Zhejiang Star TV","id":510,"service":[{"pvr_mode":"private","desc":"Zhejiang Star TV (standard)","id":1046}]},{"name":"Guangdong Southern TV","id":511,"service":[{"pvr_mode":"private","desc":"Guangdong Southern TV (standard)","id":1047}]},{"name":"Phoenix Infonews","id":512,"service":[{"pvr_mode":"private","desc":"Phoenix Infonews (standard)","id":1048}]},{"name":"Phoenix Chinese News and Entertainment","id":513,"service":[{"pvr_mode":"private","desc":"Phoenix Chinese News and Entertainment (standard)","id":1049}]},{"name":"Arirang","id":516,"service":[{"pvr_mode":"public","desc":"Arirang (bas débit)","id":1052}]},{"name":"Euronews Arabe","id":519,"service":[{"pvr_mode":"public","desc":"Euronews Arabe (standard)","id":1054}]},{"name":"Inspiration Network","id":528,"service":[{"pvr_mode":"public","desc":"Inspiration Network (standard)","id":1062}]},{"name":"God TV","id":529,"service":[{"pvr_mode":"public","desc":"God TV (standard)","id":1063}]},{"name":"Telesur","id":531,"service":[{"pvr_mode":"public","desc":"Telesur (standard)","id":1064}]},{"name":"ETB Sat","id":539,"service":[{"pvr_mode":"public","desc":"ETB Sat (standard)","id":1072}]},{"name":"TVCi","id":540,"service":[{"pvr_mode":"public","desc":"TVCi (standard)","id":1073}]},{"name":"Telenova","id":541,"service":[{"pvr_mode":"public","desc":"Telenova (standard)","id":1074}]},{"name":"Rai Uno","id":543,"service":[{"pvr_mode":"public","desc":"Rai Uno (standard)","id":1076}]},{"name":"Rai Due","id":544,"service":[{"pvr_mode":"public","desc":"Rai Due (standard)","id":1077}]},{"name":"Rai Tre","id":545,"service":[{"pvr_mode":"public","desc":"Rai Tre (standard)","id":1078}]},{"name":"TV Romania","id":552,"service":[{"pvr_mode":"public","desc":"TV Romania (standard)","id":1085}]},{"name":"Bulgaria TV","id":553,"service":[{"pvr_mode":"public","desc":"Bulgaria TV (standard)","id":1086}]},{"name":"IMED TV","id":554,"service":[{"pvr_mode":"public","desc":"IMED TV (bas débit)","id":1087}]},{"name":"ERT World","id":555,"service":[{"pvr_mode":"public","desc":"ERT World (standard)","id":1088}]},{"name":"SkyTurk","id":556,"service":[{"pvr_mode":"public","desc":"SkyTurk (bas débit)","id":1089}]},{"name":"Ulusal Kanal","id":557,"service":[{"pvr_mode":"public","desc":"Ulusal Kanal (bas débit)","id":1090}]},{"name":"Arriyadia","id":558,"service":[{"pvr_mode":"public","desc":"Arriyadia (bas débit)","id":1091}]},{"name":"TV Biznes","id":559,"service":[{"pvr_mode":"public","desc":"TV Biznes (standard)","id":1092}]},{"name":"2M Maroc","id":564,"service":[{"pvr_mode":"public","desc":"2M Maroc (standard)","id":1097}]},{"name":"TVM Europe","id":565,"service":[{"pvr_mode":"public","desc":"TVM Europe (standard)","id":1098}]},{"name":"Télévision Tunisienne","id":567,"service":[{"pvr_mode":"public","desc":"Télévision Tunisienne (standard)","id":1099}]},{"name":"Al Masriya","id":568,"service":[{"pvr_mode":"public","desc":"Al Masriya (standard)","id":1100}]},{"name":"Al Jazeera","id":569,"service":[{"pvr_mode":"public","desc":"Al Jazeera (standard)","id":1101}]},{"name":"Al Jazeera Children","id":570,"service":[{"pvr_mode":"public","desc":"Al Jazeera Children (standard)","id":1102}]},{"name":"Powertürk TV","id":582,"service":[{"pvr_mode":"public","desc":"Powertürk TV (standard)","id":1116}]},{"name":"TRT1","id":583,"service":[{"pvr_mode":"public","desc":"TRT1 (standard)","id":1117}]},{"name":"TRT Cocuk","id":586,"service":[{"pvr_mode":"private","desc":"TRT Cocuk (standard)","id":1120}]},{"name":"Kanal 24","id":589,"service":[{"pvr_mode":"public","desc":"Kanal 24 (standard)","id":1123}]},{"name":"TRT INT","id":590,"service":[{"pvr_mode":"public","desc":"TRT INT (standard)","id":1124}]},{"name":"Kanal 7 INT","id":591,"service":[{"pvr_mode":"public","desc":"Kanal 7 INT (standard)","id":1125}]},{"name":"Samanyolu TV","id":592,"service":[{"pvr_mode":"public","desc":"Samanyolu TV (standard)","id":1126}]},{"name":"TVT","id":593,"service":[{"pvr_mode":"public","desc":"TVT (standard)","id":1127}]},{"name":"Hilal TV","id":594,"service":[{"pvr_mode":"public","desc":"Hilal TV (standard)","id":1128}]},{"name":"TV5 Turkey","id":595,"service":[{"pvr_mode":"public","desc":"TV5 Turkey (standard)","id":1129}]},{"name":"Vietnam VTV4","id":606,"service":[{"pvr_mode":"public","desc":"Vietnam VTV4 (standard)","id":1140}]},{"name":"RTPi","id":611,"service":[{"pvr_mode":"public","desc":"RTPi (standard)","id":1145}]},{"name":"Arte Allemand","id":616,"service":[{"pvr_mode":"public","desc":"Arte Allemand (standard)","id":1147}]},{"name":"DW-TV","id":617,"service":[{"pvr_mode":"public","desc":"DW-TV (standard)","id":1148}]},{"name":"Suroyo TV","id":635,"service":[{"pvr_mode":"public","desc":"Suroyo TV (standard)","id":1155}]},{"name":"RTCG Sat","id":636,"service":[{"pvr_mode":"public","desc":"RTCG Sat (standard)","id":1156}]},{"name":"Kuwait TV","id":640,"service":[{"pvr_mode":"public","desc":"Kuwait TV (standard)","id":1158}]},{"name":"Kuwait TV2","id":641,"service":[{"pvr_mode":"public","desc":"Kuwait TV2 (standard)","id":1159}]},{"name":"Yemen TV","id":643,"service":[{"pvr_mode":"public","desc":"Yemen TV (standard)","id":1160}]},{"name":"Dubai TV","id":644,"service":[{"pvr_mode":"public","desc":"Dubai TV (standard)","id":1161}]},{"name":"Abu Dhabi TV","id":645,"service":[{"pvr_mode":"public","desc":"Abu Dhabi TV (standard)","id":1162}]},{"name":"Baraem","id":646,"service":[{"pvr_mode":"public","desc":"Baraem (standard)","id":1163}]},{"name":"Jordan Satellite Channel","id":648,"service":[{"pvr_mode":"public","desc":"Jordan Satellite Channel (standard)","id":1165}]},{"name":"Armenia Public TV","id":650,"service":[{"pvr_mode":"public","desc":"Armenia Public TV (standard)","id":1167}]},{"name":"Armenia TV","id":651,"service":[{"pvr_mode":"public","desc":"Armenia TV (standard)","id":1168}]},{"name":"Vesti","id":653,"service":[{"pvr_mode":"public","desc":"Vesti (standard)","id":1170}]},{"name":"Press TV","id":659,"service":[{"pvr_mode":"public","desc":"Press TV (bas débit)","id":1175}]},{"name":"NHK","id":680,"service":[{"pvr_mode":"public","desc":"NHK (standard)","id":1194},{"pvr_mode":"public","desc":"NHK (auto)","id":1195},{"pvr_mode":"public","desc":"NHK (HD)","id":1193}]},{"name":"New Tang Dynasty","id":685,"service":[{"pvr_mode":"public","desc":"New Tang Dynasty (bas débit)","id":1198}]},{"name":"Russia Today","id":694,"service":[{"pvr_mode":"public","desc":"Russia Today (standard)","id":1207}]},{"name":"Russia Today Espanol","id":695,"service":[{"pvr_mode":"public","desc":"Russia Today Espanol (bas débit)","id":1208}]},{"name":"Record Internacional","id":697,"service":[{"pvr_mode":"public","desc":"Record Internacional (standard)","id":1210}]},{"name":"Russian Al Yaum","id":698,"service":[{"pvr_mode":"public","desc":"Russian Al Yaum (bas débit)","id":1211}]},{"name":"Record News","id":699,"service":[{"pvr_mode":"public","desc":"Record News (bas débit)","id":1212}]}];

</script>
<br>

<form method="post" action="?action=programmer">

 <select name="chaine" id="chaine" onChange="selectService(selectedIndex)"></select>
 <span id="service"></span>
 <script>selectChaines();</script>

<br>

<input type="text" name="date"  value="{$a['date']}"> date<br>
<input type="text" name="heure" value="{$a['heure']}"> heure<br>
<input type="text" name="minutes" value="{$a['minutes']}"> minutes<br>
<input type="text" name="duree" value="{$a['duree']}"> durée<br>
<input type="text" name="emission" value="{$a['emission']}"> titre<br>
<input type="checkbox" name="repeat[]" value="0"> dimanche 
<input type="checkbox" name="repeat[]" value="1"> lundi 
<input type="checkbox" name="repeat[]" value="2"> mardi 
<input type="checkbox" name="repeat[]" value="3"> mercredi 
<input type="checkbox" name="repeat[]" value="4"> jeudi 
<input type="checkbox" name="repeat[]" value="5"> vendredi 
<input type="checkbox" name="repeat[]" value="6"> samedi 
<br>

 
<input type="text" name="repeat_count" value="{$a['repeat_count']}"> ocurrences<br>
<input type="submit" value="valider">
</form>


END;
	}
	protected function run_login(){

		unset($_SESSION['id']);
		unset($_SESSION['idt']);

		$magneto = new ConsoleMagneto();
		
		$login = $this->get_arg('login', false);
		$passwd = $this->get_arg('passwd', false);
		
		if($login == false){
			$this->login_form();
			return;
		}

		$status = $magneto->login($login, $passwd);
		if($status == false){
			$this->login_form();
			echo "erreur de connexion\n";
		}else{
			echo "connexion réussie ...\n";
			$_SESSION['id'] = $magneto->id();
			$_SESSION['idt'] = $magneto->idt();
			$this->location('?action=lister');
		}
	}

	protected function run_lister(){

		if(!isset($_SESSION['id']))
			return;

		$list = $this->magneto->lister();
		
		if(count($list) == 0)
			printf("pas d'enregistrement programmé\n");
		else foreach($list as $enreg){
			printf("<li><a href='?action=delete&id=%s'>X</a> - </a>%s</li>\n", $enreg->ide, (string) $enreg);
		}
	}

	protected function run_programmer(){

		$this->programmer_form();
		if(!isset($_SESSION['id']))
			return;
		echo "<pre>\n"; 
		print_r($_REQUEST);
		print_r($_SESSION);
		printf("## id = %s ; idt=%s\n", $this->magneto->id(),$this->magneto->idt());
		
		if(!isset($_REQUEST['chaine']))
			return;
		
		switch(2){
			case 1:
			$enreg = new Enregistrement();
			$args = array('chaine', 'date', 'heure', 'minutes', 'duree', 'emission', 'qualite');
			
			foreach($args as $v){
				if(!isset($_REQUEST[$v]))
					continue;
				$enreg->$v = $_REQUEST[$v];
				$_SESSION[$v] = $_REQUEST[$v];
			}

			$enreg->qualite = 'auto';
			print_r($enreg);
			break;
		
		case 2:
			$enreg = new Enregistrement();
			$enreg->date    = $this->get_arg_sess('date');
			$enreg->heure   = $this->get_arg_sess('heure');
			$enreg->minutes = $this->get_arg_sess('minutes');
			$enreg->duree   = $this->get_arg_sess('duree');

			# $enreg->repeat   = array(1,2,3);

			$enreg->chaine   = $this->get_arg_sess('chaine');
			$enreg->service   = $this->get_arg_sess('service');  
			$enreg->emission = $this->get_arg_sess('emission');
			break;
		}

		# print_r($this->magneto->infos_chaines());
		# print_r($this->magneto);

		$this->magneto->programmer($enreg);
	}
	
	protected function run_delete(){

		if(!isset($_SESSION['id']))
			return;

		$status = $this->magneto->supprimer($this->get_arg('id'));
		if($status)
			echo "suppression réussie\n";
		else
			echo "erreur suppression\n";
		$this->location('?action=lister');
	}

	public function menu(){
		printf("menu : ");
		foreach($this->params['actions'] as $action){
			printf('<a href="?action=%s">%s</a> | ', $action, $action);
		}
		echo "<br><br>\n";
	}
	
	protected function get_arg($name, $default = 'false'){
		if(isset($_REQUEST[$name]))
			return $_REQUEST[$name];

		if(isset($_SESSION[$name]))
			return $_SESSION[$name];

		return $default;
	}

	protected function get_arg_sess($name, $default = 'false'){
		if(isset($_REQUEST[$name])){
			$_SESSION[$name] = $_REQUEST[$name];
			return $_REQUEST[$name];
		}
		if(isset($_SESSION[$name]))
			return $_SESSION[$name];

		return $default;
	}
	
	protected function location($page, $delai = 4){
		flush();
		sleep($delai);
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		# header("Location: http://$host$uri/$page");
		$url = sprintf("http://$host$uri/$page");
		printf('<script type="text/javascript">location("%s");</script>', $url);
		echo "\n";
		flush();
	}
}

?>
