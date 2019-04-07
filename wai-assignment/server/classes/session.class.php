<?php

class Session
{
    private static $instance = null;

    public function __construct()
    {
        session_start();
    }

    /**
     * @return null|Session
     */
    public static function getInstance()
    {
        if(!isset(self::$instance))
        {
            self::$instance = new Session();
        }

        return self::$instance;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setProperty($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param $key
     * @return string
     */
    public function getProperty($key)
    {
        $returnValue = "";
        if(isset($_SESSION[$key]))
        {
            $returnValue = $_SESSION[$key];
        }

        return $returnValue;
    }

    /**
     * @param $key
     */
    public function endProperty($key)
    {
        if(isset($_SESSION[$key]))
        {
            unset($_SESSION[$key]);
        }
    }
}