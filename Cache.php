<?php

class Cache{
	protected $dir;
	public function __construct($dir='var/cache'){
		$this->dir = $dir;
	}
	
	public function get($key, $max_age){
		$f = sprintf('%s/%s', $this->dir, $key);
		if(!file_exists($f);
			return false;
		if(this->file_age($f)>$max_age)
			return false;
		return file_get_contents($f);
	}
	
	public function put($key, $content){
		$f = sprintf('%s/%s', $this->dir, $key);
		$status = file_put_contents($f, $content);
		if($status == false)
			throw new Exception("can't write file cache");
	}
	protected function file_age($f){
		$st = stat($f);
		return time() - $st['mtime'];
	}
}

?>
