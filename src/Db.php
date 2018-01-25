<?php
namespace Geeshoe\DbClass;

/**
 * Class Db
 * @package Geeshoe\DbClass
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
     * @var null|string $iniPath System path to the config.ini.
     */
    private $iniPath = null;

    /**
     * @var array
     */
    public $insert = array();

    /**
     * @var array
     */
    public $values = array();

    public function __construct()
    {
        $this->iniPath = dirname(__DIR__, 4) . '/DbConfig.ini';
        $settings = parse_ini_file($this->iniPath);

        if (!empty($settings['AltPath'])) {
            $this->iniPath = $settings['AltPath'];
        }
    }

    /**
     * Set up PDO instance
     *
     * The connection instance is built using the parameters in the config.ini file.
     * The connection instance is then returned to the calling method. Upon connection
     * failure, PDO Exception is thrown along with error code. See
     * http://php.net/manual/en/pdo.error-handling.php for more information.
     *
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
     * Execute MySql statement with no return.
     *
     * -Experimental method-
     *
     * @param string $sql MySQL query to be executed
     * @return void
     */
    public function execPDOQuery($sql)
    {
        $this->connect()->exec($sql);
    }

    /**
     * Execute mySql query and return single record set
     *
     * -Experimental method-
     *
     * @param string $sql MySql query to be executed
     * @param string $fetchStyle PDO fetch style to be used. I.e. PDO::FETCH_ASSOC
     * @return mixed
     */
    public function singleReturnQuery($sql, $fetchStyle)
    {
        $stmt = $this->connect()->query($sql)->fetch($fetchStyle);
        return $stmt;
    }

    /**
     * Execute a MySql query and return all affected rows.
     *
     * This method uses PDO::FETCH_ALL, if the query is only returning
     * one row, it may be better to use singleReturnQuery.
     *
     * @param string $sql MySQL statement to be executed.
     * @param string $fetchStyle Can be any PDO::FETCH_x style.
     * @return array Returns affected rows in associative array.
     */
    public function selectQuery($sql, $fetchStyle)
    {
        $stmt = $this->connect()->query($sql)->fetchAll($fetchStyle);
        return $stmt;
    }

    /**
     * Insert one or more rows into a MySQL Database
     *
     * Accepts a query string and an array formatted
     * like :key => value. Array key must have : to be bound
     * to value correctly. Use the createSqlArray function for this.
     *
     * @param string $sql MySQL statement to be executed.
     * @param array $values Associative array of values to be prepared.
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
     * @param string $sql MySQL statement to be executed.
     * @param array $values Associative array of values to be prepared.
     * @param string $fetchStyle Can be any PDO::FETCH_x style.
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
