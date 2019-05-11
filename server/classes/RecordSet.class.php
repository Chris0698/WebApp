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

    public function getRecordSet($sql, $params = null)
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
     * @param null $params
     * @return bool|false|PDOStatement|string|null
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
    /**
     * @param $sql
     * @param null $params
     * @return array|bool|false|PDOStatement|null
     */
    public function getRecordSet($sql, $params = null)
    {
        $stmt = parent::getRecordSet($sql, $params);
        $recordSet = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $numberOfRecords = count($recordSet);

        if($numberOfRecords == 0)
        {
            $results = [];
        }
        else
        {
            $results = $recordSet;
        }

        return array
        (
            "rowCount" => $numberOfRecords,
            "results" => $results
        );
    }
}