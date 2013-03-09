<?php

use AmperXIL\Parser;
use AmperXIL\Source\XILFileSource;

require_once "autoload.php";

defined('TIME_START') || define('TIME_START',microtime());

try
{
	$parser = new Parser( XILFileSource::find("prototype.xil") );

	$parser->parse();

	printf("OK. Peak memory used: %fMB. Execution time: %fs\n", memory_get_peak_usage(true) / 1024 / 1024, round((microtime() - TIME_START), 4));
}
catch (AmperXIL\Parser\ParserException $e)
{
	printf("Parse error: [%s, line %d] %s\n", $e->source->getIndexName(), $e->symbol->token->line, $e->getMessage());
}
catch (\Exception $e)
{
	printf("Error [%s] %s\n", get_class($e), $e->getMessage());
}