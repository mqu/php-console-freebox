<?php

/* geany_encoding=ISO-8859-15 */

require_once('CURL.php');

class ConsoleFree{

	protected $login;
	protected $passwd;

	protected $id;
	protected $idt;

	protected $curl; # handle IO with curl library


	public function __construct(){
		$this->curl = new Curl();
	}

	/**
		login method

			- on OK, return true,
			- on error : return false
	*/
	public function login($login=null, $passwd=null){

		$login_url = 'https://subscribe.free.fr/login/login.pl';

		if($login != null){
			$this->login = $login;
			$this->passwd = $passwd;
		}
		$vars = array(
			"login"  => $this->login,
			"pass"   => $this->passwd
		);

		$data = $this->curl->post($login_url, $vars);

		# Location: https://adsl.free.fr/compte/console.pl?id=XXXX&idt=YYYY
		if(preg_match('#^Location:.+?https://adsl.free.fr/compte/console.pl\?id=(.+?)&idt=(.+?)$#m', $data, $values)){

			$this->id = $values[1];
			$this->idt = $values[2];
			$this->idt = substr($this->idt, 0, 16);  # remove leading chars (\n, \r)

			return true;

		} else
			throw new Exception ("ConsoleFree::login() : connexion error");
	}

	public function id(){
		return $this->id;
	}

	public function idt(){
		return $this->idt;
	}

	public function set_from_session($id, $idt){
		$this->id = $id;
		$this->idt = $idt;
	}
}

?>
