<?php
namespace dBase;

class db
{
    private $connection = null;

    private function connect(){
        if (!isset($this->connection)){

            /*
             * Change the path of $ini to reflect the path of the config.ini.
             * It is recommended to keep config.ini outside of your webroot.
             */
            $ini = parse_ini_file("/var/htdocs/dev/dbClass/config.ini");
            $this->connection = new \PDO(
                'mysql:dbname='.$ini['dataBase'].
                ';host='.$ini['hostName'].':'
                .$ini['port'], $ini['userName'], $ini['passWord']
            );
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $this->connection;
    }

    //Accepts a query string and any PDO::FETCH_ style
    public function selectQuery($sql, $fetchStyle){
        $stmt = $this->connect()->query($sql)->fetchAll($fetchStyle);
        return $stmt;
    }
    //Accepts a query string and an array formatted
    //like :key => value. Array key must have : to be bound
    //to value correctly. Use the createSqlArray function for this.
    public function insert($sql, $values){
        $stmt = $this->connect()->prepare($sql);
        foreach($values as $arrayKey => $arrayValue){
            $stmt->bindValue($arrayKey, $arrayValue);
        }
        $stmt->execute();
    }
    //Functions the same as insert, however will return a query array
    //after supplying any PDO::FETCH_style.
    public function fetch($sql, $values, $fetchStyle){
        $stmt = $this->connect()->prepare($sql);
        foreach($values as $arrayKey => $arrayValue){
            $stmt->bindValue($arrayKey, $arrayValue);
        }
        $stmt->execute();
        $results = $stmt->fetch($fetchStyle);
        return $results;
    }

    public $insert = array();
    public $values = array();

    //Creates two arrays from one userSuppliedData array. The two new arrays
    //are formatted to be used in conjunction with the insert & fetch functions.
    public function createSqlArray($userSuppliedData){
        foreach(array_keys($userSuppliedData) as $key){
            $this->insert[] = $key;
            $this->values[':'.$key] = $userSuppliedData[$key];
        }
    }
    //Creates a mySql INSERT query for use with the insert function.
    //As this function is dependant on the insert & value arrays,
    //createSqlArray must be executed prior to calling createSqlInsertStatement.
    //Or the insert & value arrays must be populated by some other manner.
    public function createSqlInsertStatement($insertTable){
        return 'INSERT INTO `'.$insertTable.'`('.implode(', ',$this->insert).') VALUE ('.implode(', ', array_keys
            ($this->values)).')';
    }
    //@TODO - Create update / remove mySql statements.
}