<?php

/**
 * Class Registry
 */
abstract class Registry
{
    /**
     * Registry constructor.
     */
    private function __construct()
    {
    }

    /**
     * Abstract method to get a value in a registry
     * @param $key, the key to be get a value
     */
    protected abstract function Get($key);

    /**
     * Abstract method to set a value using a key
     * @param $key, the key to set a value.
     * @param $value, the value to be set.
     */
    protected abstract function Set($key, $value);
}