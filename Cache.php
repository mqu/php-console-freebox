<?php

class Cache{
	protected $dir;
	public function __construct($dir='var/cache'){
		$this->dir = $dir;
		
		if(!file_exists($dir)){
			$status = mkdir($dir, $mode = 0777, $recursive=true);
			if($status == false)
				throw new Exception("Cache: can't create directory ($dir)");
		}
		if(!is_writable($dir))
			throw new Exception("Cache: this directory is not writable ($dir)");
	}
	
	# cache age in minutes, default 1h
	public function get($key, $max_age = 60){
		$max_age *=  60;
		$f = $this->key_to_filename($key);
		if(!file_exists($f))
			return false;
		if($this->file_age($f)>$max_age)
			return false;
		return file_get_contents($f);
	}
	
	public function put($key, $content){
		$f = $this->key_to_filename($key);
		$status = file_put_contents($f, $content);
		if($status == false)
			throw new Exception("Cache: can't write file cache ($f)");
	}

	protected function file_age($f){
		$st = stat($f);
		return time() - $st['mtime'];
	}
	
	protected function key_to_filename($key){
		return sprintf('%s/%s', $this->dir, md5($key));
	}
}

?>
