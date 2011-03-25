<?php

class CURL {
	protected $callback = null;
	protected $user_agent = 'lynx';

	function setCallback($func_name) {
		$this->callback = $func_name;
	}
	
	function doRequest($method, $url, $vars) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		# curl_setopt($ch,CURLOPT_WRITEFUNCTION, array($this, "write_function"));
		# curl_setopt($ch,CURLOPT_READFUNCTION, array($this, "read_function"));
		# curl_setopt($ch,CURLOPT_PROGRESSFUNCTION, array($this, "curl_progress_func"));

		# curl_setopt($ch, CURLOPT_VERBOSE, true); verbose mode

		curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');

		if ($method == 'POST') {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		if ($data) {
			if ($this->callback != null)
			{
				$callback = $this->callback;
				$this->callback = false;
				return call_user_func($callback, $data);
			} else {
				return $data;
			}
		} else {
			return curl_error($ch);
		}
	}

	public function get($url) {
		return $this->doRequest('GET', $url, 'NULL');
	}
	
	public function post($url, $vars) {
		return $this->doRequest('POST', $url, $vars);
	}

	public function write_function($ch, $string){
		echo "write function\n";
		return strlen($string);
	}

	public function read_function($ch, $string){
		echo "read function\n";
		return strlen($string);
	}

	public function curl_progress_func($fp, $dltotal, $dlnow, $ultotal, $ulnow){
		echo "curl_progress_func()\n";
	}
}

?>
