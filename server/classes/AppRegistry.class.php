<?php
require_once ("Registry.class.php");
require_once ("DBConnection.class.php");
require_once ("setEnv.php");

/**
 * Class AppRegistry
 */
class AppRegistry extends Registry
{
    private $values = array();
    private static $instance = null;    //singleton

    /**
     * AppRegistry constructor.
     */
    public function __construct()
    {
        $this->OpenSystemConfigFile();
    }

    /**
     * @return AppRegistry instance that is in use
     */
    private static function Instance()
    {
        if(!self::$instance)
        {
            self::$instance = new Self();
        }

        return self::$instance;
    }

    /**
     * Used through this class to set values
     * @param $key, the item, to set
     * @param $value, the value to set
     */
    protected function Set($key, $value)
    {
        $this->values[$key] = $value;
    }

    /**
     * Get the value stored in a key or null
     * @param $key, of the value
     * @return value, of the key or null if not exists
     */
    protected function Get($key)
    {
        return isset($this->values[$key]) ? $this->values[$key] : null;
    }

    /**
     * Opens the system config XML file
     */
    private function OpenSystemConfigFile()
    {
        $filename = CONFIGLOCATION;

        if(file_exists($filename))
        {
            $temp = simplexml_load_file($filename);

            foreach ($temp as $key=>$value)
            {
                $this->Set($key, trim($value));
            }
        }
    }

    public static function GetUsername()
    {
        return self::Instance()->Get("username");
    }

    public static function GetPassword()
    {
        return self::Instance()->Get("password");
    }

    public static function GetDNS()
    {
        return self::Instance()->Get("dns");
    }

    public static function GetBasePath()
    {
        return self::Instance()->Get("basepath");
    }

    public static function getDBConnection()
    {
        return DBConnection::getConnection();
    }
}