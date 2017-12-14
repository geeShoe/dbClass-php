<?php

namespace dBase;

class db
{
    private $connection = null;

    private function connect(){
        if (!isset($this->connection)){
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
    //@TODO - Create universal mySql statements.
}