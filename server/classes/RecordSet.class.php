<?php

require_once ("DBConnection.class.php");

/**
 * Class RecordSet
 * Basic ResetSet for database queries where results does not need to be formatted
 */
class RecordSet
{
    protected $connection = null;
    protected $stmt = null;

    /**
     * RecordSet constructor.
     */
    public function __construct()
    {
        $this->connection = DBConnection::getConnection();
    }

    /**
     * @param $sql, the SQL of the query
     * @param $params, paramteres of the query, likely to be an array for prepared statement
     * @return PDOStatement of the connection
     */
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
 * Class XMLRecordSet
 * Gets the data from the database and formats it into XML
 *  Note: this is unused in this web app
 */
class XMLRecordSet extends RecordSet
{
    /**
     * @param $sql string containing the database query
     * @param string $elementName the name of the outer most tag
     * @param null $params for the SQL query, likely to be an array for prepared statements
     * @return string containing the XML data
     */
    public function getRecordSet($sql, $elementName = "element", $params = null)
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
}

/**
 * Class JSONRecordSet
 * Gets the data from the database and formats it into JSON
 */
class JSONRecordSet extends RecordSet
{
    /**
     * @param $sql string containing the query
     * @param null $params for the SQL query, likely to be an array for prepared statements
     * @return array containing the number of results and the results them self
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