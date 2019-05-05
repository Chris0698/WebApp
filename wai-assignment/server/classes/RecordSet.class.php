<?php

require_once ("DBConnection.class.php");
require_once ("Film.class.php");

/**
 * Class RecordSet
 */
class RecordSet
{
    protected $connection = null;
    protected $stmt = null;

    public function __construct()
    {
        $this->connection = DBConnection::getConnection();
    }

    public function getRecordSet($sql, $elementName = "ResultSet", $params = null)
    {
        if(is_array($params))
        {
            $this->stmt = $this->connection->prepare($sql);
            $this->stmt->execute($params);
        }
        else
        {
            $this->stmt = $this->connection->query($sql);
        }

        return $this->stmt;
    }
}

/**
 * Class PDORecordSet
 */
class PDORecordSet extends RecordSet
{
    public function getRecordSet($sql, $elementName = "element", $params = null)
    {
        return parent::getRecordSet($sql);
    }
}

/**
 * Class XMLRecordSet
 */
class XMLRecordSet extends RecordSet
{
    /**
     * @param $sql
     * @param string $elementName
     * @return false|PDOStatement|string|null
     */
    public function getRecordSet($sql, $elementName = "element", $params = null)
    {
        $stmt = null;
        try
        {
            $stmt = parent::getRecordSet($sql);
            $returnValue = "";
            header("content-type: text/xml");
            $returnValue.= "<?xml version=\"1.0\" encoding=\"ISO - 8859 - 1\"?>\n";
            $returnValue.= "<$elementName>s\n";
            while ($data = $stmt->fetchObject(PDO::FETCH_ASSOC))
            {
                $returnValue.= "\t<$elementName>\n";
                foreach ($data as $key => $value)
                {
                    $returnValue.= "\t\t<$key>$value</$key>\n";
                }
                $returnValue.= "\t<$elementName>\n";
            }
            $returnValue.= "<$elementName>s\n";
            return $returnValue;
        }
        catch (Exception $exception)
        {
            return $exception->getMessage();
        }
    }
}

/**
 * Class JSONRecordSet
 */
class JSONRecordSet extends RecordSet
{
    public function getRecordSet($sql, $elementName = "ResultSet", $params = null)
    {
        try
        {
            $stmt = parent::getRecordSet($sql, $elementName, $params);
            $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $numberOfResults = count($recordSet);

            return json_encode
            (
                array
                (
                    //"$elementName" => array
                    //(
                    "rowCount" => $numberOfResults,
                    "results" => $recordSet
                    //)
                ),
                JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT
            );
        }
        catch (Exception $exception)
        {
            return $exception->getMessage();
            //return json_decode('"Error" : "'.$exception->getMessage().'""');
        }
    }
}