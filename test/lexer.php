<?php

use AmperXIL\Parser;
use AmperXIL\Source\XILFileSource;

require_once "autoload.php";

try
{
	$parser = new Parser( XILFileSource::find("prototype.xil") );

	$parser->parse();

	echo 'OK';
}
catch (AmperXIL\Parser\ParserException $e)
{
	printf("Parse error: [%s, line %d] %s\n", $e->source->getIndexName(), $e->symbol->token->line, $e->getMessage());
}
catch (\Exception $e)
{
	printf("Error [%s] %s\n", get_class($e), $e->getMessage());
}