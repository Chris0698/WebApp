<?php

/**
 * Class FileWriter writes anything to a text file
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
            echo "<p>An error occured when saving data: ".$exception->getMessage()."</p>";
        }
    }
}