<?php

namespace AmperXIL;

abstract class Source 
{
	/**
	 * Abstract implementation
	 */
	 
	abstract public function getIndexName();
	abstract public function getContent();

	/**
	 * Concrete impmentation
	 */
	
	// A constant is essentially a reference
	// to a token.
	protected $_constants = array();

	public function setConstant($name, Token $token)
	{
		$this->_constants[(string)$name] = $token;
	}

	public function hasConstant($name)
	{
		return array_key_exists($name, $this->_constants);
	}

	public function constant( $name )
	{
		$this->hasConstant( $name ) ? $this->_constants[$name] : null;
	}

	public function getContentAsLines()
	{
		return preg_split("/(\r\n|\n|\r)/", $this->getContent());
	}
}