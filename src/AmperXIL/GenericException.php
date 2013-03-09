<?php

namespace AmperXIL;

class GenericException extends \Exception 
{
    /**
     * Overload the construgtor to allow printf() args
     */
    
    public function __construct( /* ... */ )
    {
        if (!func_num_args())
            $aAllArgs = array("");
        else            
            $aAllArgs = func_get_args();

        $szString = array_shift($aAllArgs);

        $szFinalString = vsprintf($szString, $aAllArgs);

        parent::__construct($szFinalString);        
    }
}
