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

    public function __construct()
    {
        $this->OpenSystemConfigFile();
    }



    private static function Instance()
    {
        if(!self::$instance)
        {
            self::$instance = new Self();
        }

        return self::$instance;
    }

    protected function Set($key, $value)
    {
        $this->values[$key] = $value;
    }

    protected function Get($key)
    {
        return isset($this->values[$key]) ? $this->values[$key] : null;
    }

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