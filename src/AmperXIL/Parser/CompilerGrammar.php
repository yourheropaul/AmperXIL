<?php

namespace AmperXIL\Parser;

use AmperXIL\Source;
use AmperXIL\Destination;
use AmperXIL\Symbol;
use AmperXIL\Symbol\SymbolException;

class CompilerGrammar
{
	public static function handleNamespaceSymbol( Source &$source, 
												  Symbol &$symbol, 
												  Destination &$destination, 
												  \DOMNode &$xml_document  )
	{
		try 
		{
			$namespace = $ns_uri = null;
			$count = $symbol->findAssignment($namespace, $ns_uri, true);

			if (!$namespace->isStringOrQuery() || !$ns_uri->isStringOrQuery())
				throw new SymbolException("Namespace expects both arguments to be strings");

			$destination->addNamespace($namespace->value(), $ns_uri->value());

			return ($count+1);
		}
		catch (SymbolException $e)
		{
			throw new ParserException($source, $symbol, $e->getMessage());
		}
	}

	public static function handleImportSymbol( Source &$source, 
											   Symbol &$symbol, 
											   Destination &$destination, 
											   \DOMNode &$xml_document  )
	{
		$el = $destination->createChildElement('handle-import', null, $xml_document);

		$destination->addAttributeToElement("file", __FILE__, $el);
		return 2;
	}

	public static function handleIncludeSymbol( Source &$source, 
												Symbol &$symbol, 
												Destination &$destination, 
												\DOMNode &$xml_document  )
	{
		$el = $destination->createChildElement('handle-include', null, $xml_document);

		$destination->addAttributeToElement("file", __FILE__, $el);
		return 2;
	}

	public static function handleConstantDefSymbol( Source &$source, 
													Symbol &$symbol, 
													Destination &$destination, 
													\DOMNode &$xml_document  )
	{

		try 
		{
			$const_name = $const_value = null;

			$count = $symbol->findAssignment($const_name, $const_value, true);

			if (!$const_name->isConstant())
				throw new SymbolException("Invalid constant name");

			$source->setConstant($const_name->value(), $const_value);

			return ($count+1);
		}
		catch (SymbolException $e)
		{
			throw new ParserException($source, $symbol, $e->getMessage());
		}
	}
}