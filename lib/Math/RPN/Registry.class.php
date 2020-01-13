<?php
namespace Math\RPN;

class Registry implements \ArrayAccess, \IteratorAggregate {
    private $registry = [];
    private $callbacks = [];

    function __construct(&$registry = NULL) {
        if ($registry !== NULL) $this->registry = &$registry;
    }

    private function set_callback ($name, $callback) {
        if (!key_exists($name, $this->callbacks)) $this->callbacks[$name] = [];
        $this->callbacks[$name][] = $callback;
    }

    function lookup ($name, $callback = false) {
        $value = $this[$name];
        if ($value === NULL) {
            if (is_callable($callback)) $this->set_callback($name, $callback);
            return NULL;
        }

        if (is_callable($callback)) $callback($name, $value);
        return $value;
    }

    function __get ($name) { return key_exists($name, $this->registry) ? $this->registry[$name] : NULL; }
    function __set ($name, $value) {
        $this->registry[$name] = $value;
        if (key_exists($name, $this->callbacks)) {
            $callbacks = $this->callbacks[$name];
            unset($this->callbacks[$name]);
            while ($cb = array_shift($callbacks)) $cb($name, $value);
        }
    }
    function __isset ($name) { return key_exists($name, $this->registry); }
    function __unset ($name) { unset($this->registry[$name]); }

    function offsetGet    ($i)     { return $this->$i; }
    function offsetSet    ($i, $v) { return $this->$i = $v; }
    function offsetExists ($i)     { return isset($this->$i); }
    function offsetUnset  ($i)     { unset($this->$i); }

    public function getIterator () { return new \ArrayIterator($this->registry); }

}

