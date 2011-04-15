<?php

/* geany_encoding=ISO-8859-15 */

require_once('Cache.php');
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

		header("Content-Type: text/html; charset=iso8859-15");
	
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
				
				$cache = new Cache();
				$key = 'info-chain-json';
				$this->infochaine_json = $cache->get($key, 60*24);
				if($this->infochaine_json == false){
					$this->infochaine_json = $this->magneto->infos_chaines_json();
					$cache->put($key, $this->infochaine_json);
				}
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

<br>

attention : l'interface Web est en cours de développement et pas encore pleinement fonctionnelle. Vous devez attendre encore qq jours pour qu'elle soit stabilisée :
<ul>
<li>la fonction programmer est non fonctionnelle,</li>
<li>la fonction effacer est aussi non fonctionnelle.</li>
</ul>
<br>



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
		$info_chaine = $this->infochaine_json;
		
		echo <<<END


<script>

$info_chaine

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
