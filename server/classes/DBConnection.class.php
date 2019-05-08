<?php

require_once ("AppRegistry.class.php");

/**
 * Class DBConnection
 */
class DBConnection
{
    private static $connection = null;

    private function __construct()
    {
    }

    public function __destruct()
    {

    }

    /**
     * Get a connection to the database
     * @return null|PDO
     */
    public static function getConnection()
    {
        $dns = AppRegistry::GetDNS();
        $username = "";
        $password = "";

        if(!self::$connection)
        {
            try
            {
                $options = array
                (
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                );

                self::$connection = new PDO($dns, $username, $password, $options);

            }
            catch (PDOException $exception)
            {
                echo "<p>PDO Exception: " . $exception->getMessage();
            }
        }

        return self::$connection;
    }
}