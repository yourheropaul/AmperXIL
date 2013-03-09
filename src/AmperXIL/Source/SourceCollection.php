<?php

namespace AmperXIL\Source;

use AmperXIL\GenericException;
use AmperXML\Source;

class SourceCollection implements \Iterator
{
	// Storage for the sourc objects
	protected $_stack = array();

	/**
	 * 
	 * Create a collection given either an array
	 * of source objects, or a single object.
	 * 
	 */
    
    public function __construct( $objects = null )
    {
        if ($objects)
        {
            if (!is_array($objects)) $objects = array($objects);

            foreach ($objects as $object)
            	$this->assertSourceObject($object);
            
            $this->m_stack = $objects;
        }
    }

    /**
     * 
     * Check that a given object is a instance
     * of a source
     * 
     * @param objects
     * 
     */

    protected function assertSourceObject( $object )
    {
    	if (!is_object($object))
    		throw new GenericException("[%s::%s] Passed datum is not an object", __CLASS__, __FUNCTION__);

    	if (!is_a($object, 'AmperXIL\\Source'))
    		throw new GenericException("[%s::%s] passed object is not a valid source", __CLASS__, __FUNCTION__);
    }

    /**
     * 
     * Add a source object to the collection
     * 
     * @param $object
     * 
     */
    
    public function add( $object )
    {
    	$this->assertSourceObject($object);

        $this->_stack[] = $object;
    }

    /*
    ** Iterator implementation 
    */
    
    public function rewind() 
    {    
        reset($this->_stack);
    }

    public function current() 
    {
        $var = current($this->_stack);    
        return $var;
    }

    public function key() 
    {
        $var = key($this->_stack);        
        return $var;
    }

    public function next() 
    {
        $var = next($this->_stack);
        return $var;
    }

    public function valid() 
    {
        $var = $this->current() !== false;
        return $var;
    }
    
    /*
    ** Get stack length
    */
    
    public function count()
    {
        return count($this->_stack);
    }
}