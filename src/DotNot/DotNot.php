<?php

namespace DotNot;

use ArrayAccess;
use DotNot\Exceptions\ArrayKeyNotFoundException;
use DotNot\Exceptions\DotNotException;
use DotNot\Exceptions\NoMoreRecursionLevels;
use DotNot\Exceptions\NotImplementedException;
use DotNot\Exceptions\ObjectPropertyNotFound;
use Symfony\Component\Process\Exception\InvalidArgumentException;


/**
 * Description of Access
 *
 * @author Dev
 */
class DotNot implements ArrayAccess
{
    private $root;
    private $current;
    
    /**
     * 
     * @param mixed $root
     */
    public function __construct($root)
    {
        $this->root = $root;
        $this->current = $this->root;
    }
    
    /**
     * @see ArrayAccess
     * 
     * @param stirng $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * @see ArrayAccess
     * 
     * @param stirng $offset
     * @return boolean
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @see ArrayAccess
     * 
     * @param string $offset
     * @param mixed $value
     * @throws NotImplementedException
     */
    public function offsetSet($offset, $value)
    {
        throw new NotImplementedException;
    }

    /**
     * @see ArrayAccess
     * 
     * @param string $offset
     * @throws NotImplementedException
     */
    public function offsetUnset($offset) 
    {
        throw new NotImplementedException;
    }
    
    /**
     * Recursively access data
     * 
     * @param string|array $path
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get($path)
    {
        $this->current = $this->root;
        
        if (is_string($path)) {
            $path = explode('.', $path);
        }
        
        if (is_array($path)) {
            return $this->rget($path);
        }
        
        throw new InvalidArgumentException("Path should be a dot-string or an array");
    }
    
    /**
     * Test if can get the given path
     * 
     * @param string $path
     * @return boolean
     */
    public function has($path)
    {
        // if we get no exception, the given path exists
        try {
            $this->get($path);
            
            return true;
        } catch (DotNotException $ex) {
            return false;
        }
    }

    /**
     * Actually do recursion on attribute access
     * 
     * @param array $path
     * @return mixed
     */
    private function rget(array $path)
    {
        $this->current = $this->getAttribute(array_shift($path));
        
        // last level, return found value
        if (count($path) === 0) {
            return $this->current;
        }
        
        return $this->rget($path);
    }
    
    /**
     * "Polymorphic" value accessor (array or object)
     * 
     * @param mixed $key
     * @return mixed
     * @throws NoMoreRecursionLevels
     */
    private function getAttribute($key)
    {
        if (is_array($this->current)) {
            return $this->getArrayKey($key);
        }
        
        if (is_object($this->current)) {
            return $this->getObjectProperty($key);
        }
        
        throw new NoMoreRecursionLevels;
    }
    
    /**
     * Get array value, if exists
     * 
     * @param mixed $key
     * @return mixed
     * @throws ArrayKeyNotFoundException
     */
    private function getArrayKey($key)
    {
        if (array_key_exists($key, $this->current)) {
            return $this->current[$key];
        }
        
        throw new ArrayKeyNotFoundException($key);
    }
    
    /**
     * Get object property, if exists
     * 
     * @param mixed $property
     * @return mixed
     * @throws ObjectPropertyNotFound
     */
    private function getObjectProperty($property)
    {
        if (property_exists($this->current, $property)) {
            return $this->current->{$property};
        }
        
        $getter = sprintf('get%s', ucfirst($property));
        
        if (is_callable([$this->current, $getter])) {
            return call_user_func([$this->current, $getter]);
        }
        
        throw new ObjectPropertyNotFound($property);
    }
}

