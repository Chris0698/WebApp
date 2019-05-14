<?php

require_once ("AppRegistry.class.php");

/**
 * Class DBConnection for database connection
 */
class DBConnection
{
    private static $connection = null;

    /**
     * DBConnection constructor.
     */
    private function __construct()
    {
    }

    /**
     *
     */
    public function __destruct()
    {

    }

    /**
     * Get a connection to the database
     * @return PDO connection for success
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