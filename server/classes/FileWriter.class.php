<?php

/**
 * Class FileWriter writes anything to a file
 */
class FileWriter
{
    /**
     * FileWriter constructor. Set private to stop normal class creation
     */
    private function __construct()
    {
    }

    /**
     * Write any text to a file
     * @param $fileName, name of the file
     * @param $text, content of the file
     */
    public static function WriteToFile($fileName, $text)
    {
        try
        {
            $fileHandle = fopen($fileName, "ab");
            fwrite($fileHandle, $text);
            fclose($fileHandle);
        }
        catch (Exception $exception)
        {
            echo "<p>An error occurred when saving data: ".$exception->getMessage()."</p>";
        }
    }

    /**
     * Writes exceptions generated throughout the app to a file
     * @param $fileName
     * @param Exception $exception the exception thrown
     */
    public static function WriteExceptionToFile($fileName, Exception $exception)
    {
        $fileHandle = fopen($fileName, "ab");
        $date = date('D M j G:i:s T Y');
        fwrite($fileHandle, "$date");
        fwrite($fileHandle,$exception->getMessage().PHP_EOL);
        fclose($fileHandle);
    }
}