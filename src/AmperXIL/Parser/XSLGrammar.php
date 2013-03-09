<?php

namespace AmperXIL\Parser;

use AmperXIL\Source;
use AmperXIL\Destination;
use AmperXIL\Symbol;

class XSLGrammar
{
	public static function handleMatchingTemplateSymbol( Source &$source, 
														 Symbol &$symbol, 
														 Destination &$destination, 
														 \DOMNode &$xml_document  )
	{
		$xml_document = $destination->createChildElement('template', Destination::NAMESPACE_XSL, $xml_document);

		$destination->addAttributeToElement("match", $symbol->token->match[1], $xml_document);

		return 1;
	}

	public static function handleRenderedComment( Source &$source, 
												  Symbol &$symbol, 
												  Destination &$destination, 
												  \DOMNode &$xml_document  )
	{
		// Create an XSL comment
		$comment = $destination->createChildElement('comment', 
													Destination::NAMESPACE_XSL, 
													$xml_document, 
													$symbol->token->match[1]);

		return 1;
	}
}