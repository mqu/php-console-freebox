<?php

require_once('ConsoleMagneto.php');

class Page {
	protected $params = array(
	  'actions' => array("login","lister", "programmer", "supprimer"),
	);
	protected $magneto;

	public function __construct(){
		session_start();
		header("Content-Type: text/html; charset=UTF-8");
	}

	public function run(){
		$this->menu();
		
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
		$a['minutes'] = 5+$this->get_arg($v, date('i'));

		echo <<<END
		
Programmer : 
<form method="post" action="?action=programmer">
<input type="text" name="chaine" value="{$a['chaine']}"> chaine<br>
<input type="text" name="qualite" value="{$a['qualite']}"> qualite<br>
<input type="text" name="date"  value="{$a['date']}"> date<br>
<input type="text" name="heure" value="{$a['heure']}"> heure<br>
<input type="text" name="minutes" value="{$a['minutes']}"> minutes<br>
<input type="text" name="duree" value="{$a['duree']}"> durée<br>
<input type="text" name="emission" value="{$a['emission']}"> titre<br>
<input type="text" name="repeat" value="{$a['repeat']}"> répétions<br>
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
			echo "connexion réussie\n";
			unset($_SESSION['magneto']);
			$_SESSION['id'] = $magneto->id();
			$_SESSION['idt'] = $magneto->idt();
		}
	}

	protected function run_lister(){

		if(!isset($_SESSION['id']))
			return;

		printf("## id = %s ; idt=%s<br>\n", $this->magneto->id(),$this->magneto->idt());
		$list = $this->magneto->lister();
		
		foreach($list as $enreg){
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

			$enreg->chaine   = $this->get_arg_sess('chaine');   # si PB accent, remplacer par expression reg (ex : 'Public S.*nat')
			$enreg->qualite  = $this->get_arg_sess('qualite', 'standard');   # auto, standard, bas-débit, auto, TNT, TNT-HD (idem accents).
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
		flush(); sleep(2);
		# $this->location('?action=lister');
	}

	public function menu(){
		printf("menu : ");
		foreach($this->params['actions'] as $action)
			printf('<a href="?action=%s">%s</a> | ', $action, $action);
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
	
	protected function location($page){
		$host  = $_SERVER['HTTP_HOST'];
		$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
		header("Location: http://$host$uri/$page");
	}
}

?>
