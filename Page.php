<?php

/* geany_encoding=ISO-8859-15 */

require_once('Cache.php');
require_once('ConsoleMagneto.php');
require_once('Enregistrement.php');
require_once('EnregistrementRecurrent.php');

class Page {
	protected $params = array(
	  'actions' => array("login","lister", "programmer", "supprimer"),
	);
	protected $magneto;

	public function __construct(){
		@session_start();
		
	}

	protected function header(){

		header("Content-Type: text/html; charset=iso8859-15");
	
		echo <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="keywords" content="enregistrement, freebox, révolution, freebox révolution, free.fr, programmation, récurrent, magneto, magnetoscope-numerique, console freebox" />
	<meta name="description" content="programmation d'enregistrement sur la freebox révolution" />
	<title>enregistrement récurent freebox révolution</title>
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

		if(!$this->is_ssl())
			header(sprintf("Location: %s", $this->my_url()));

		$this->header();

		$this->menu();

		try{
			$action = $this->get_arg('action', false);
			
			if($action == false){
				echo "ce site vous permet de programmer des enregistrements récurrents ; veuillez vous connecter (<a href='?action=login'>login</a>) ...<br>\n";
				# $this->location('?action=login', 3);
				$this->location($this->my_url() . '?action=login', 3);
				return false;
			}
			if($action != 'login' && isset($_SESSION['id'])){
				$this->magneto = new ConsoleMagneto();
				$this->magneto->set_from_session($_SESSION['id'], $_SESSION['idt']);
				
				$cache = new Cache();
				$key = 'info-chain-json';
				$this->infochaine_json = $cache->get($key, 60*2);
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
			$this->location('?action=login', 4);
		}
		catch(Exception $e){
			printf("une erreur s'est produite : %s<br>\n", $e->getMessage());
		}

		$this->footer();
	}
	
	protected function login_form(){
		
		$login = $this->get_arg_sess('login', '');
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
<li>la fonction programmer est fonctionnelle et gère la récurrence :-)</li>
<li>les conflits d'enregistrements ne sont pas encore gérés</li>
</ul>

<b>liens</b>
<ul>
<li><a href="https://github.com/mqu/php-console-freebox/wiki">aide</a> en ligne</li>
<li>déposer et suivre les <a href="https://github.com/mqu/php-console-freebox/issues">BUGs</a></li>
</ul>

<b>infos de version</b>
<ul>
<li>vous utilisez la version 0.2 + qq petits correctifs</li>
</ul>
<br>

END;
	}

	protected function programmer_form(){
		
		$args = array('chaine', 'qualite', 'duree', 'emission', 'repeat');
		foreach($args as $v)
			$a[$v] = $this->get_arg($v, '');

		$a['date'] = $this->get_arg('date', date('d/m/Y'));
		$a['heure'] = $this->get_arg('heure', date('h'));
		$a['minutes'] = min(59, 5+$this->get_arg('minute', date('i')));
		$a['repeat_count'] = $this->get_arg('repeat_count', '');

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
<input type="checkbox" name="repeat[]" value="0"> dim 
<input type="checkbox" name="repeat[]" value="1"> lun 
<input type="checkbox" name="repeat[]" value="2"> mar 
<input type="checkbox" name="repeat[]" value="3"> mer 
<input type="checkbox" name="repeat[]" value="4"> jeu 
<input type="checkbox" name="repeat[]" value="5"> ven 
<input type="checkbox" name="repeat[]" value="6"> sam 
<br>
<br>
<input type="text" name="repeat_count" value="{$a['repeat_count']}"> ocurrences<br>
<input type="submit" value="valider">
</form>


END;
	}

	protected function form_supprimer(){
		
		$expr = $this->get_arg_sess('expr', '');
		echo <<<END
		
Supprimer : 
<form method="post" action="?action=supprimer">
<input type="hidden" name="doit" value="true">
<input type="text" name="expr" value="$expr"> expression portant sur le titre<br>
<i>expression (*test*, *France 2* ; * est le caractère générique ; tout supprimer : '*')</i><br>
<input type="submit" value="valider">
</form>

END;
	}

	protected function run_supprimer(){
		$this->form_supprimer();
		if($this->get_arg('doit', false) == 'true'){
			$expr = $this->get_arg('expr');
			$expr = str_replace('*', '.*', $expr);
			$this->magneto->supprimer_expr($expr);
		}
		echo "<br>\n";
		$this->run_lister();
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
			$this->location('?action=lister', 2);
		}
	}

	protected function run_lister(){

		if(!isset($_SESSION['id']))
			return;

		$list = $this->magneto->lister();
		
		if(count($list) == 0)
			printf("pas (plus) d'enregistrement programmé\n");
		else foreach($list as $enreg){
			printf("<li><a href='?action=delete&id=%s'>X</a> - </a>%s</li>\n", $enreg->ide, (string) $enreg);
		}
	}

	protected function run_programmer(){

		$this->programmer_form();
		if(!isset($_SESSION['id']))
			return;

		echo "<pre>\n"; 
		# printf("## id = %s ; idt=%s\n", $this->magneto->id(),$this->magneto->idt());
		# print_r($_SESSION);
		# print_r($_REQUEST);
		
		if(!isset($_REQUEST['chaine']))
			return;
		

		$enreg = new EnregistrementRecurrent();
		$enreg->date    = $this->get_arg_sess('date');
		$enreg->heure   = $this->get_arg_sess('heure');
		$enreg->minutes = $this->get_arg_sess('minutes');
		$enreg->duree   = $this->get_arg_sess('duree');

		$enreg->chaine   = $this->get_arg_sess('chaine');
		$enreg->service  = $this->get_arg_sess('service');  
		$enreg->emission = $this->get_arg_sess('emission');


		# gestion de la récurrence :
		$enreg->repeat = $this->get_arg_sess('repeat', array());
		$count=$this->get_arg_sess('repeat_count', false);
		if($count>1){
			foreach ($enreg->repeat($count) as $event){
				$this->magneto->programmer($event);
				echo $event . "\n";
				flush();
			}
			echo "\n$count enregistrements effectués.";
		} else {
			$this->magneto->programmer($enreg);
			echo "enregistrement effectué.";
		}
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
	
	protected function my_url($proto='https'){
		return sprintf('%s://%s%s/', $proto, $_SERVER['HTTP_HOST'], rtrim(dirname($_SERVER['PHP_SELF']), '/\\'));
	}
	
	protected function is_ssl(){
		if($_SERVER['HTTPS'] == 'on')
			return true;
		return false;
	}

	protected function location($page, $delai = 4){
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		# header("Location: http://$host$uri/$page");
		
		if(!preg_match('#http.\:#', $page))
			$url = sprintf("http://$host$uri/$page");
		else
			$url = $page;
		printf('<script type="text/javascript">new_loc_timeout("%s", %s);</script>', $url, $delai*1000);
		echo "\n";
		flush();
	}
}

?>
