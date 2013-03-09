<?php

namespace AmperXIL\Source;

class XILFileSource extends \AmperXIL\Source
{
	protected $_path;

	public function __construct( $input_file_path )
	{
		if (!file_exists($input_file_path) || !is_readable($input_file_path))
			throw new XILFileException("Path '%s' doesn't exist or isn't readable");

		$this->_path = $input_file_path;
	}

	public function getIndexName()
	{
		return basename($this->_path);
	}

	public function getContent()
	{
		return file_get_contents($this->_path);
	}

	public static function find( $glob_path )
	{
		$collection = new SourceCollection;

		foreach (glob($glob_path) as $path)
			$collection->add(new self($path));

		return $collection;
	}
}