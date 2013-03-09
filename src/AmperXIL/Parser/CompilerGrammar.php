<?php

namespace AmperXIL\Parser;

use AmperXIL\Source;
use AmperXIL\Destination;
use AmperXIL\Symbol;

class CompilerGrammar
{
	public static function handleNamespaceSymbol( Source &$source, 
												  Symbol &$symbol, 
												  Destination &$destination, 
												  \DOMNode &$xml_document  )
	{
		$count = count($symbol->line_tokens);

		if ($count < 2)
			throw new ParserException($source, $symbol, "Too few arguments passed to namespace");

		if ($count > 3)
			throw new ParserException($source, $symbol, "Too few arguments passed to namespace");

		if ($count == 3 && !$symbol->line_tokens[1]->isAssignment())
			throw new ParserException($source, $symbol, "Namespace expects '[namespace] = [url]' syntax");

		$namespace = $symbol->line_tokens[0];
		$ns_uri	   = $symbol->line_tokens[$count-1];

		if (!$namespace->isStringOrQuery() || !$ns_uri->isStringOrQuery())
			throw new ParserException($source, $symbol, "Namespace expects both arguments to be strings");

		$destination->addNamespace($namespace->value(), $ns_uri->value());

		return (count($symbol->line_tokens)+1);
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
		$el = $destination->createChildElement('handle-constant-def', null, $xml_document);

		$destination->addAttributeToElement("file", __FILE__, $el);

		return 1;
	}
}