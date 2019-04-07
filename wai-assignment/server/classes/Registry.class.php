<?php

abstract class Registry
{
    private function __construct()
    {
    }

    protected abstract function Get($key);

    protected abstract function Set($key, $value);
}