<?php

namespace AmperXIL;

class Token 
{
	///////////////////////////////////////////////////
	// Constants
	///////////////////////////////////////////////////
	
	const TOKEN_NAMESPACE 				= "T_NAMESPACE";
	const TOKEN_IMPORT		 			= "T_IMPORT";
	const TOKEN_INCLUDE 				= "T_INCLUDE";
	const CONSTANT_DEF 					= "T_CONSTANT_DEF";

	const TOKEN_WHITESPACE 				= "T_WHITESPACE";

	const TOKEN_BLOCK_COMMENT_START		= "T_BC_START";
	const TOKEN_BLOCK_COMMENT_END		= "T_BC_END";
	const TOKEN_LINE_COMMENT			= "T_COMMENT";
	const TOKEN_RENDERED_COMMENT		= "T_RENDERED_COMMENT";

	const TOKEN_STRING_DOUBLE			= "T_STRING_DBL";
	const TOKEN_STRING_SINGLE			= "T_STRING_SGL";

	const TOKEN_ASSIGNMENT				= "T_ASSIGNMENT";

	const TOKEN_MATCHING_TEMPLATE		= "T_TEMP_MATCH";
	const TOKEN_NAMED_TEMPLATE			= "T_TEMP_NAMED";

	const TOKEN_QUERY					= "T_QUERY";

	///////////////////////////////////////////////////
	// Object implementation
	///////////////////////////////////////////////////
	
	public $length = 0,
		   $match  = array(),
		   $type   = null,
		   $line   = 0,
		   $indent = 0;

	public function __construct( $length, $match, $type, $line )
	{
		$this->length = $length;
		$this->match  = $match;
		$this->type   = $type;
		$this->line   = $line;
	}	

	///////////////////////////////////////////////////
	// Qualification methods
	///////////////////////////////////////////////////
	
	public function isWhitespace()
	{
		return ($this->type == static::TOKEN_WHITESPACE);
	}

	public function isComment()
	{
		return ($this->type == static::TOKEN_BLOCK_COMMENT_START ||
			    $this->type == static::TOKEN_BLOCK_COMMENT_END ||
			    $this->type == static::TOKEN_LINE_COMMENT );
	}

	public function isIgnorable()
	{
		return ($this->isWhitespace() || $this->isComment());
	}

	public function isString()
	{
		return ($this->type == static::TOKEN_STRING_DOUBLE || $this->type == static::TOKEN_STRING_SINGLE);
	}

	public function isQuery()
	{
		return ($this->type == static::TOKEN_QUERY);
	}

	public function isStringOrQuery()
	{
		return ($this->isString() || $this->isQuery());
	}

	public function isAssignment()
	{
		return ($this->type == static::TOKEN_ASSIGNMENT);
	}

	public function value()
	{
		switch ($this->type)
		{
			case static::TOKEN_STRING_DOUBLE:
			case static::TOKEN_STRING_SINGLE:
				return $this->match[1];

			case static::TOKEN_QUERY:
				return $this->match[0];
		}

		return null;
	}
}