<?php

namespace AmperXIL;

class Assembler
{
	protected $_tokens = array(),
			  $_root = null;

	/**
	 * 
	 * Create an instance of the Symbol object,
	 * which functions as a root, trunk or leaf
	 * in a symbol tree.
	 * 
	 * @param $tokens 	Array of Token objects
	 * 
	 * @return self
	 * 
	 */

	public function __construct( array $tokens )
	{
		$this->_root = new Symbol;
		$this->_tokens = $tokens;

		// Reset the array
		reset($this->_tokens);

		// Run the recursive assembler	
		$this->assembleNode( $this->_root );
	}

	/**
	 * 
	 * Fetch the root Symbol from the assembler
	 * object.
	 * 
	 * @return Symbol
	 * 
	 */

	public function getRootSymbol()
	{
		return $this->_root;
	}

	/**
	 * 
	 * Given a parent Symbol node and an indentation
	 * depth, recurse over the embedded list of tokens
	 * and create a tree of synbols.
	 * 
	 * @param &$parent 	Symbol object
	 * @param $depth 	Indentation depth
	 * 
	 * @return null
	 * 
	 */

	protected function assembleNode( Symbol &$parent, $depth = 0 )
	{
		$previous_node = null;

		for ($token = current($this->_tokens); $token; $token = next($this->_tokens))
		{
			// Ignore whitespace
			if ($token->isIgnorable()) continue;

			// Handle indentation trees
			if ($previous_node && $token->indent > $depth)
			{
				$this->assembleNode( $previous_node, $token->indent );
				continue;
			}
			else if ($token->indent < $depth)
			{
				prev($this->_tokens);
				return;
			}

			// Create a child record
			$parent->children[] = new Symbol( $parent, 	$token );

			// Find the array key
			$key = count($parent->children)-1;

			// Set the previous node
			$previous_node = &$parent->children[$key];

			// Add the line tokens
			for ($i = (key($this->_tokens) + 1); $i < count($this->_tokens); $i++)
			{
				if ($token->line != $this->_tokens[$i]->line)
					break;

				if ($this->_tokens[$i]->isIgnorable())
					continue;

				$previous_node->line_tokens[] = &$this->_tokens[$i];
			}
		}
	}
}