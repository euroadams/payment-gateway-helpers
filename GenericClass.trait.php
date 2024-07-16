<?php

trait GenericClass{

    
	/* Getter method for fetching private static property */
	public static function getStatic($staticPropName){
		
		return self::${$staticPropName};
		
	}


    protected function bind_multi_constructor($funcNameDel = ''){

        //Multiple Constructor Listener 
                
        $args = func_get_args();
        $numOfArgs = func_num_args();

        !$funcNameDel? ($funcNameDel = '__construct_') : '';

        if(method_exists($this, ($funcName = $funcNameDel.$numOfArgs)))
            call_user_func_array(array($this, $funcName), $args);

    }



}


?>