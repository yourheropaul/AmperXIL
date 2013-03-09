<?php

namespace AmperXIL;

abstract class Source 
{
	abstract public function getIndexName();
	abstract public function getContent();

	public function getContentAsLines()
	{
		return preg_split("/(\r\n|\n|\r)/", $this->getContent());
	}
}