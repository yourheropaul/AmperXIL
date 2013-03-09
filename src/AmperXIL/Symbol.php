<?php

namespace AmperXIL;

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
}