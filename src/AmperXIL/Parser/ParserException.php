<?php

namespace AmperXIL\Parser;

class ParserException extends GenericParserException
{
	public $source = null,
    	   $symbol = null;

    public function __construct( /* ... */ )
    {
        if (func_num_args() < 3)
        	throw new genericParserException("Parser exception called with too few arguments");

        $aAllArgs = func_get_args();

        $this->source = array_shift($aAllArgs);
        $this->symbol = array_shift($aAllArgs);

        $szString = array_shift($aAllArgs);
        $szFinalString = vsprintf($szString, $aAllArgs);

        parent::__construct($szFinalString);        
    }
}