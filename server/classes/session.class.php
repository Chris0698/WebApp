<?php

/**
 * Class Session for handling user sessions
 */
class Session
{
    private static $instance = null;

    /**
     * Session constructor.
     */
    public function __construct()
    {
        session_start();
    }

    /**
     * @return Session, containing the session data.
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
     * Set a value of the session
     * @param $key, of the session
     * @param $value, the value to the set
     */
    public function setProperty($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get the value of a session
     * @param $key, key of the session
     * @return string, containing the session data
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
     * End the property of the session
     * @param $key, key of the session to be ended
     */
    public function endProperty($key)
    {
        if(isset($_SESSION[$key]))
        {
            unset($_SESSION[$key]);
        }
    }
}