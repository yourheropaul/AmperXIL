<?php

namespace AmperXIL;

// TODO:
// - Handle named template calls

class Lexer 
{
	protected static $_terminals = array(

		// Parser directives
        "/^(#define)/" 						=> Token::CONSTANT_DEF,
        "/^(#if-?def(ined)?)/" 				=> 'directive_ifdef',
        "/^(#if-?n(ot)?-?def(ined)?)/" 		=> 'directive_ifndef',
        "/^(#else)/"		 				=> 'directive_else',
        "/^(#end)/"			 				=> 'directive_end',
        "/^(#each)/"			 			=> 'directive_end',

		// Compiler directives
        "/^(namespace)\s+/" 					=> Token::TOKEN_NAMESPACE,
        "/^(import)\s+/" 						=> Token::TOKEN_IMPORT,
        "/^(include)\s+/" 						=> Token::TOKEN_INCLUDE,
        "/^(constant)\s+/" 						=> Token::CONSTANT_DEF,

        "/^if\s+(.+)/"							=> 'if',
        "/^else\s*if\s+(.+)/"					=> 'else_if',
        "/^else\s+(.+)/"						=> 'else',

        "/^((xsl:?)?text)/"						=> 'xsl_text',
        "/^((xsl:?)?value-?of)/"				=> 'xsl_value-of',
        "/^((xsl:?)?apply-?(templates?)?)/"		=> 'xsl_apply-templates',
        "/^(apply)/"							=> 'xsl_apply-templates',

        "/^((xsl:?)?end)/"						=> 'end',

        "/^\s*(\.|this)\s*/"					=> "dot",

        "/^\+\s*([a-zA-z0-9\/-]+)/" 		=> Token::TOKEN_MATCHING_TEMPLATE,
        "/^\-\s*([a-zA-z0-9\/:-]+)/" 		=> Token::TOKEN_NAMED_TEMPLATE,

        "/^(<.+>)/"							=> 'literal',

        '/^(\[)/'							=> 'array_start',
        '/^(\])/'							=> 'array_end',

        '/^"(.+)"/'							=> Token::TOKEN_STRING_DOUBLE,
        "/^'(.+)'/"							=> Token::TOKEN_STRING_SINGLE,

        "/^\s*(:?=)\s*/"					=> Token::TOKEN_ASSIGNMENT,

        "/^(\s+)/" 							=> Token::TOKEN_WHITESPACE,

        "/^&&\s+(.+)$/" 					=> Token::TOKEN_RENDERED_COMMENT,
        "/^(&\s+.+)$/" 						=> Token::TOKEN_LINE_COMMENT,
        "/^(&\*)/" 							=> Token::TOKEN_BLOCK_COMMENT_START,
        "/^(\*&)/" 							=> Token::TOKEN_BLOCK_COMMENT_END,

        "/^\s*(\()\s*/"						=> "open_bracket",
        "/^\s*(\))\s*/"						=> "close_bracket",

        '/^_([a-z_-]+)/' 					=> "weak_symbol",
        '/^\$([a-z_:-]+)/' 					=> "variable",
        '/^@([a-z_:-]+)/' 					=> "attribute",
        "/^([A-Z_-]+)/" 					=> "constant",
        "/^([a-zA-Z:_\/\[\]=\.-]+)/" 		=> Token::TOKEN_QUERY
    );

	protected $_source_lines = array(),
			  $_block_comment = false;

	public function __construct( array $source_lines )
	{
		$this->source_lines = $source_lines;
	}

	public function run() 
	{
	    $tokens = array();
	 
	    foreach($this->source_lines as $number => $line) 
	    {
	        $offset = 0;
	        $indent = strlen($line);

	        // Count the indents
	        $line  = ltrim($line);
	        $indent = $indent - strlen($line);

	        while($offset < strlen($line)) 
	        {
	            $result = $this->match($line, $number, $offset);

	            if($result === false) 
	            {
	            	if ($this->_block_comment)
	            		break;

	                throw new GenericException("Unable to parse line " . ($number+1) . ".");
	            }

	            // Handle closing token
	            if ($result->type == Token::TOKEN_BLOCK_COMMENT_END)
	            	$this->_block_comment = false;

	            $result->indent = $indent;

	            // Store token only if not a comment
	            if (!$this->_block_comment)
		            $tokens[] = $result;

	            // Start block comments
	            if ($result->type == Token::TOKEN_BLOCK_COMMENT_START)
	            	$this->_block_comment = true;

	            $offset += $result->length;

	             // Line comments
	            if ($result->type == Token::TOKEN_LINE_COMMENT)
	            	break;
	        }
	    }
	 
	    return $tokens;
	}

	protected function match($line, $number, $offset) 
	{
	    $string = substr($line, $offset);

	    foreach(static::$_terminals as $pattern => $name) 
	    {
	        if (preg_match($pattern, $string, $matches)) 
	        {
	            return new Token (
	            	strlen($matches[0]),
	                $matches,
	                $name,
	                $number+1
	            );
	        }
	    }
	 
	    return false;
	}
}