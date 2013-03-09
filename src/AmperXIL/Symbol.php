<?php

namespace AmperXIL;

use AmperXIL\Symbol\SymbolException;

class Symbol
{
	// Reference to parent object
	public $parent = null;

	// Token container
	public $token = null;

	// Subsequent line tokens
	public $line_tokens = array();

	// Children
	public $children = array();

	public function __construct( $parent = null, $token = null )
	{
		if ($parent)
			$this->parent = &$parent;

		if ($token)
			$this->token = &$token;
	}

	/**
	 * 
	 * Look through the line tokens and
	 * find an assignment made on the same
	 * line
	 * 
	 * @param &$operand 	
	 * @param &$value 	
	 * @param $accept_no_assignment
	 * 
	 */

	public function findAssignment( &$operand, &$value, $accept_no_assignment = false )
	{
		$count = count($this->line_tokens);

		if (($accept_no_assignment && $count < 2) || (!$accept_no_assignment && $count < 3))
			throw new SymbolException("Too few arguments passed to %s", $this->token->type);

		if ( !$accept_no_assignment && !$this->line_tokens[1]->isAssignment())
			throw new SymbolException("%s expects '[operand] = [value]' syntax", $this->token->type);

		$value_index = ($accept_no_assignment && !$this->line_tokens[1]->isAssignment()) ? 1 : 2;

		$operand = $this->line_tokens[0];
		$value	 = $this->line_tokens[$value_index];

		return $count;
	}
}