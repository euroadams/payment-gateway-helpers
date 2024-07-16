<?php


class ObjectProtection{
	
	private $fallBackVal;
	
	/** Constructor **/
	public function __construct($fallBackVal = ''){
		
		$this->fallBackVal = $fallBackVal;
		
	}
	
	
	/** Destructor **/
	public function __destruct(){
		
		
	}
	
	
	/**** Property overloading (works only in object context) ****/

	// Magic methods for silently handling data write to undefined or inaccessible(protected or private) properties of an object
	public function __set($name, $val){
		
		return $this->fallBackVal;
		
		
	}
	
	// Magic methods for silently handling data read from undefined or inaccessible(protected or private) properties of an object
	public function __get($name){
		
		return $this->fallBackVal;
		
		
	}
	
	// Magic methods for silently handling property existence check calls (using isset() or empty()) on undefined or inaccessible(protected or private) properties of an object
	public function __isset($name){
		
		return $this->fallBackVal;
		
		
	}
	
	// Magic methods for silently handling property unset calls (using unset()) on undefined or inaccessible(protected or private) properties of an object
	public function __unset($name){
		
		
	}
	
	
	/**** Method overloading (works both in object and static context)****/

	// Magic methods for silently handling calls to undefined methods in object context
	public function __call($name, $args){
		
		return $this->fallBackVal;
		
		
	}
	
	// Magic methods for silently handling calls to undefined methods in static context
	public static function __callStatic($name, $args){
		
		return $this->fallBackVal;
		
		
	}
	
	
}


?>