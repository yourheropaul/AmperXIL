<?php

namespace AmperXIL;

use AmperXIL\Parser\GenericParserException;

class Parser
{
	protected static $_token_handlers = array(
				Token::TOKEN_NAMESPACE 		   => array( 'class' => 'CompilerGrammar', 'method' => 'handleNamespaceSymbol' ),
				Token::TOKEN_IMPORT    		   => array( 'class' => 'CompilerGrammar', 'method' => 'handleImportSymbol' ),
				Token::TOKEN_INCLUDE 		   => array( 'class' => 'CompilerGrammar', 'method' => 'handleIncludeSymbol' ),
				Token::TOKEN_CONSTANT_DEF 	   => array( 'class' => 'CompilerGrammar', 'method' => 'handleConstantDefSymbol' ),
				Token::TOKEN_MATCHING_TEMPLATE => array( 'class' => 'XSLGrammar', 'method' => 'handleMatchingTemplateSymbol' ),
				Token::TOKEN_RENDERED_COMMENT  => array( 'class' => 'XSLGrammar', 'method' => 'handleRenderedComment' ),
				Token::TOKEN_LITERAL		   => array( 'class' => 'XSLGrammar', 'method' => 'handleLiteral' )
			);

	protected $_sources,
			  $_destinations = array();

	public function __construct( Source\SourceCollection $sources )
	{
		if (!$sources->count())
			throw new GenericParserException("No files to parse");

		$this->_sources = $sources;
	}

	public function parse()
	{
		foreach ($this->_sources as $source)
		{
			// Get the content split into lines
			$lines = $source->getContentAsLines();

			// Create a lexer
			$lexer = new Lexer($lines);

			// Find the source tokens
			$raw_tokens = $lexer->run();

			// Assemble to tokens into a tree
			$tree = new Assembler($raw_tokens);

			// Create a new destination object
			$destination = new Destination;

			$this->processSymbolTree($source, $tree->getRootSymbol(), $destination, $destination->root);

			echo $destination->document->saveXML();
		}
	}

	public function processSymbolTree( Source $source, Symbol $symbol, Destination $destination, \DOMNode $xml_document )
	{
		$child_start   = 0;
		$tokens_parsed = 1;

		if ($symbol->token)
		{
			if (array_key_exists($symbol->token->type, self::$_token_handlers))
			{
				$method = self::$_token_handlers[$symbol->token->type]['method'];
				$class  = 'AmperXIL\\Parser\\'.self::$_token_handlers[$symbol->token->type]['class'];

				$tokens_parsed = $class::$method( 
									$source, 
									$symbol, 
									$destination, 
									$xml_document
							  );
			}
			else
			{
				$xml_document = $destination->createChildElement('unexpected-symbol', null, $xml_document);

					$destination->addAttributeToElement("line", $symbol->token->line, $xml_document);
					$destination->addAttributeToElement("token", $symbol->token->type, $xml_document);
					$destination->addAttributeToElement("line-tokens", count($symbol->line_tokens), $xml_document);

					for ($match = 0; $match < count($symbol->token->match); $match++)
						$destination->addAttributeToElement("match-".$match, $symbol->token->match[$match], $xml_document);
			}
		}

		for ($i = $child_start; $i < count($symbol->children); )
		{
			$i += $this->processSymbolTree( $source, $symbol->children[$i], $destination, $xml_document );
		}

		return $tokens_parsed;
	}
}