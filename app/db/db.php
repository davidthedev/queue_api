<?php

class DB {

    private $connection;

    public function __construct()
    {
        try {
            $driver = 'mysql';
            $dbName = 'dbname=firmstep';
            $dbHost = 'host=127.0.0.1';
            // change these
            $username = '';
            $password = '';
            $dns = $driver . ':' . $dbHost . ';' . $dbName;
            $this->connection = new PDO($dns, $username, $password);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Select from table
     *
     * @param  string $table table name
     * @param  string $where where clause
     * @return array
     */
    public function select($table, $where = '')
    {
        $sql = 'SELECT * FROM ' . $table;

        if ($where) {
            $sql .= ' WHERE ' . $where;
        }

        $prepare = $this->connection->prepare($sql);

        try {
            $prepare->execute();
        } catch (Exception $e) {
            $e->getMessage();
        }

        return $prepare->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insert into table
     *
     * @param  string   $table  table name
     * @param  array    $values values to import
     * @return boolean
     */
    public function insert($table, $values)
    {
        $values['queuedDate'] = time();

        $columns = implode(', ', array_keys($values));
        $valuesJoined = implode(', :', array_keys($values));

        $sql  = 'INSERT INTO ' . $table . '(' . $columns . ')';
        $sql .= ' VALUES (:' . $valuesJoined . ')';

        $prepare = $this->connection->prepare($sql);

        $prepare->bindValue(":type", $values['type']);
        $prepare->bindValue(":service", ucfirst($values['service']));
        $prepare->bindValue(":firstName", $values['firstName']);
        $prepare->bindValue(":lastName", $values['lastName']);
        $prepare->bindValue(":organization", $values['organization']);
        $prepare->bindValue(":queuedDate", (string) date('Y-m-d H:i:s'));

        try {
            $prepare->execute();
        } catch (Exception $e) {
            $e->getMessage();
            return false;
        }

        return true;
    }
}
