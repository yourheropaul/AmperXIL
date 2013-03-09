<?php

namespace AmperXIL;

class Destination
{
	const NAMESPACE_XSL = "http://www.w3.org/1999/XSL/Transform";

	public $document = null,
		   $root = null,
		   $namespaces = array('xsl' => self::NAMESPACE_XSL);

	public function __construct()
	{
		$this->initialiseDocument();	
	}

	protected function initialiseDocument()
	{
		$this->document = new \DOMDocument('1.0', 'utf-8');
		$this->document->formatOutput = true;

		// Create stylesheet node
		$this->root = $this->document->createElementNS(self::NAMESPACE_XSL, 'xsl:stylesheet');

		$this->document->appendChild($this->root);

		// Add basic attributes
		$this->addAttributeToElement('version', '1.0', $this->root);
	}

	public function addNamespace( $namespace, $uri )
	{
		$this->root->setAttributeNS('http://www.w3.org/2000/xmlns/' ,'xmlns:'.$namespace, $uri);

		$this->namespaces[$namespace] = $uri;
	}

	public function addAttributeToElement( $name, $value, $element )
	{
		// Add basic attributes
		$attr = $this->document->createAttribute($name);
		$attr->value = $value;

		$element->appendChild($attr);
	}

	public function createChildElement( $name, $namespace = self::NAMESPACE_XSL, $parent = null, $value = null)
	{
		if ($namespace)
			$element = $this->document->createElementNS($namespace, $name);
		else
			$element = $this->document->createElement($name);

		if ($parent)
			$parent->appendChild($element);
		else
			$this->root->appendChild($element);

		if ($value)
			$element->appendChild($this->document->createTextNode($value));

		return $element;
	}
}