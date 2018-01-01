<?php
namespace geeshoe\dbClass;

/**
 * Class Db
 * @package dBase
 */
class Db
{
    /**
     * @var null
     */
    private $connection = null;

    /**
     * Set iniPath before using this class
     *
     * @var string $iniPath System path to the config.ini.
     */
    private $iniPath = "/path/to/config.ini";

    /**
     * @var array
     */
    public $insert = array();

    /**
     * @var array
     */
    public $values = array();

    /**
     * @return null|\PDO
     */
    private function connect()
    {
        if (!isset($this->connection)) {
            $ini = parse_ini_file($this->iniPath);
            $this->connection = new \PDO(
                'mysql:dbname='.$ini['dataBase'].
                ';host='.$ini['hostName'].':'.$ini['port'],
                $ini['userName'],
                $ini['passWord']
            );
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $this->connection;
    }

    /**
     * Execute mySql query with no return.
     *
     * -Experimental method-
     *
     * @param string $sql MySQL query to be executed
     */
    public function exec($sql)
    {
        $this->connect()->query($sql);
    }

    public function singleReturnQuery($sql, $fetchStyle)
    {
        $stmt = $this->connect()->query($sql)->fetch($fetchStyle);
    }

    /**
     * Accepts a query string and any PDO::FETCH_ style
     *
     * @param $sql
     * @param $fetchStyle
     * @return array
     */
    public function selectQuery($sql, $fetchStyle)
    {
        $stmt = $this->connect()->query($sql)->fetchAll($fetchStyle);
        return $stmt;
    }

    /**
     * Accepts a query string and an array formatted
     * like :key => value. Array key must have : to be bound
     * to value correctly. Use the createSqlArray function for this.
     *
     * @param $sql
     * @param $values
     * @return void
     */
    public function insert($sql, $values)
    {
        $stmt = $this->connect()->prepare($sql);
        foreach ($values as $arrayKey => $arrayValue) {
            $stmt->bindValue($arrayKey, $arrayValue);
        }
        $stmt->execute();
    }

    /**
     * Functions the same as insert, however will return a query array
     * after supplying any PDO::FETCH_style.
     *
     * @param $sql
     * @param $values
     * @param $fetchStyle
     * @return mixed
     */
    public function fetch($sql, $values, $fetchStyle)
    {
        $stmt = $this->connect()->prepare($sql);
        foreach ($values as $arrayKey => $arrayValue) {
            $stmt->bindValue($arrayKey, $arrayValue);
        }
        $stmt->execute();
        $results = $stmt->fetch($fetchStyle);
        return $results;
    }

    /**
     * Creates two arrays to be used in conjunction with the insert and fetch functions.
     * Accepts two arguments. $typeOfStatement is used to determine if the function is returning
     * a 'insert' array or an 'update' array.
     * The second argument is an the userSuppliedData array which is used to populate the insert and values array.
     *
     * @param $typeOfStatement
     * @param $userSuppliedData
     * @return void
     */
    public function createSqlArray($typeOfStatement, $userSuppliedData)
    {
        foreach (array_keys($userSuppliedData) as $key) {
            if ($typeOfStatement == 'insert') {
                $this->insert[] = $key;
            } elseif ($typeOfStatement == 'update') {
                $this->insert[] = '`' . $key . '`' . ' = :' . $key;
            }
            //@TODO - Throw exception if wrong $typeOfStatement is entered.
            $this->values[':'.$key] = $userSuppliedData[$key];
        }
    }

    /**
     * Creates a mySql INSERT query for use with the insert function.
     * As this function is dependant on the insert & value arrays,
     * createSqlArray must be executed prior to calling createSqlInsertStatement.
     * Or the insert & value arrays must be populated by some other manner.
     *
     * @param $insertTable
     * @return string
     */
    public function createSqlInsertStatement($insertTable)
    {
        $statement = 'INSERT INTO `'.$insertTable.'`('
            . implode(', ', $this->insert) .
            ') VALUE ('
            . implode(', ', array_keys($this->values)) .
            ')';
        return $statement;
    }

    /**
     * Creates a mySql UPDATE query for use with the insert function.
     * As this function is dependant on the insert & value arrays,
     * createSqlArray must be executed prior to calling createSqlUpdateStatement.
     * Or the insert & value arrays must be populated by some other manner.
     *
     * @param $updateWhichTable
     * @param $updateByWhatColumn
     * @param $updateWhatId
     * @return string
     */
    public function makeSqlUpdateStatements($updateWhichTable, $updateByWhatColumn, $updateWhatId)
    {
        return 'UPDATE `'.$updateWhichTable.'` SET ' . implode(", ", $this->insert) . ' WHERE `'
            .$updateByWhatColumn.'` = ' . $updateWhatId;
    }
    //@TODO - Create update / remove mySql statements.
}
