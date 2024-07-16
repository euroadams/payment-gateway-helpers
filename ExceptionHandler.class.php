<?php


class ExceptionHandler{
	
	/** Member variable **/
	private $isDev = false;
	
	/** Constructor **/
	public function __construct($isDev = false){
		
		$this->isDev = $isDev;
		
		error_reporting(E_ALL); //Ensure we report all errors
	
	
		set_exception_handler(array($this, 'handleExceptions')); // Set the exception handler

		
		/** Force all PHP errors to be thrown as exception(converts PHP errors to ErrorException instances) **/
		set_error_handler(function($level, $message, $file = '', $line = 0, $context = []){
			
			if(error_reporting() & $level)
				throw new ErrorException($message, 0, $level, $file, $line);
			
		});
		
		
		/** Handle fatal exception errors separately since they are not caught by the standard error handler **/
		register_shutdown_function(function(){
			
			if (!is_null($error = error_get_last()) && $this->isFatal($error['type'])){
			
				$this->handleExceptions(new ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']));
			
			}
			
		});
	
		
	}
	
	
	
	
	/** Destructor **/
	public function __destruct(){
		
		
	}
	
	
	
	
	
	/** Method for handling the rendering of all thrown exception to visitors and developer **/
	public function handleExceptions($e){
		
		global $SITE;
		
		error_log($e); //Log the exception error
		http_response_code(500); //Set the http response code
		
		/** 
			If we are displaying errors, then it's assumed we are in the development mode, 
			thus we render the thrown exception error 
			otherwise we render a custom message in production mode
		**/
		echo 
		
		'<div>'.
			($this->isDev? '<pre>'.$e.'</pre>' : '
				
				<h1>Internal Server Error(500)</h1>
				<div>An internal server error has occurred <br>Please try again later</div>'
			).'
		</div>';
		
		$developerName = 'Euroadams';
		$developerEmail = 'xpresors@gmail.com';
		$subject = 'SITE EXCEPTION ERROR OCCURED';
		$message = 'Hello '.$developerName.'\n\n This is to notify you that an exception error was detected. Please find details below: \n\n\n '.$e;
		$footer = 'You received this mail because this address was listed as our developer under the name; '.$developerName.'. Please kindly ignore if otherwise.';
		
		$this->isDev? '' : $SITE->sendMail(array('to'=>$developerEmail.'::'.$developerName, 'subject'=>$subject, 'body'=>$message, 'footer'=>$footer));
		
	}
	
	
	
	
	/** Method for checking if the thrown exception error is fatal **/
    protected function isFatal($type){
	
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
	
    }
	
	


}




?>