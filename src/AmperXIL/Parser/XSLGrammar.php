<?php

namespace AmperXIL\Parser;

use AmperXIL\Source;
use AmperXIL\Destination;
use AmperXIL\Symbol;

use AmperXIL\GenericException;

class XSLGrammar
{
	// Storage for current symbol destination, for callbacks
	public static $__currentDestination = null;

	public static function handleMatchingTemplateSymbol( Source &$source, 
														 Symbol &$symbol, 
														 Destination &$destination, 
														 \DOMNode &$xml_document  )
	{
		$xml_document = $destination->createChildElement('template', Destination::NAMESPACE_XSL, $xml_document);

		$destination->addAttributeToElement("match", $symbol->token->match[5], $xml_document);

		// Check for mode
		if (strlen($symbol->token->match[1]))
		{
			$mode = $symbol->token->match[4];

			// Check for namespace
			if (strlen($symbol->token->match[3]))
			{
				if (!$destination->hasNamespace($symbol->token->match[3]))
					throw new ParserException($source, $symbol, "Unregistered namespace '%s'", $symbol->token->match[3]);

				$mode = $symbol->token->match[3] . ":" . $mode;
			}

			$destination->addAttributeToElement("mode", $mode, $xml_document);
		}

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

	public static function handleLiteral( Source &$source, 
										  Symbol &$symbol, 
										  Destination &$destination, 
										  \DOMNode &$xml_document  )

	{
		//////////////////////////////////////////////
		// Handle errors
		//////////////////////////////////////////////
		
		set_error_handler(function($number, $error)
		{		  
		    throw new GenericException("%s", $error);
		});

		try 
		{
			$placeholder = $symbol->token->match[0];

			//////////////////////////////////////////////
			// Check for element end-tag on its own
			//////////////////////////////////////////////
			
			if (preg_match('/^<\/([a-z][a-zA-Z0-9_-]+:)?([a-z][a-zA-Z0-9_-]+)/', $placeholder))
			{
				return 1;
			}
			
			//////////////////////////////////////////////
			// Basic self-closing on trailing >
			//////////////////////////////////////////////
			

			$literal =  substr_count($placeholder, '>') == 1 ? 
						preg_replace('/([^\/])>\s*$/',"$1 />",$symbol->token->match[0]) :
						$symbol->token->match[0];

			//////////////////////////////////////////////
			// Handle namspaces (omitted by default)
			//////////////////////////////////////////////
			
			self::$__currentDestination = $destination;

			$literal = preg_replace_callback('/<([a-z][a-zA-Z0-9_-]+):([a-z][a-zA-Z0-9_-]+)/', 
											 function( $matches) {
											 	if (!XSLGrammar::$__currentDestination->hasNamespace($matches[1]))
											 		throw new GenericException("Namespace prefix '%s' not defined", $matches[1]);

											 	return sprintf('<%s:%s xmlns:%s="%s"', 
											 						$matches[1], 
											 						$matches[2],
											 						$matches[1], 
											 						XSLGrammar::$__currentDestination->getNamespace($matches[1]));
											 }, $literal);

			//////////////////////////////////////////////
			// Create and add the document fragment
			//////////////////////////////////////////////
			
			$fragment = $destination->document->createDocumentFragment(); 
	        $fragment->appendXML($literal);

	        $xml_document->appendChild($fragment);

	        $xml_document = $xml_document->lastChild;

	        //////////////////////////////////////////////
			// Remove the error handling
			//////////////////////////////////////////////

	        restore_error_handler();
	    }
	    catch (GenericException $e)
	    {
	    	throw new ParserException($source, $symbol, $e->getMessage());
	    }

        return 1;
	}	
}