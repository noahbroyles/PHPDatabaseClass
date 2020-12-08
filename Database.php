<?php

class DBResult {

    private $queryResults;

    function __construct($data) {
        $this->queryResults = $data;
    }

    function fetchAll() {
        return $this->queryResults;
    }

    function fetchOne() {
        return $this->queryResults[0];
    }
}

class Database {

    private $mysqliConnection;

    /**
     * Database constructor.
     * @param string $host the address where the database server hosts
     * @param string $user the username for the database login
     * @param string $password the password to login to the database
     * @param string $dbname the database name
     * @throws Exception Throws an exception if there is a promlem connecting to the database
     */
    public function __construct ($host, $user, $password, $dbname) {
        $this->mysqliConnection = mysqli_connect($host, $user, $password, $dbname);
        $this->mysqliConnection->autocommit(true);
        if ($this->mysqliConnection->connect_error) {
            throw new Exception("Database Connection Failed", 69);
        }
    }


    private function getTypeString($params) {
        $typeString = "";
        foreach ($params as $p) {
            $type = gettype($p);
            switch ($type) {
                case "string":
                    $types .= "s";
                    break;
                case "integer":
                    $types .= "i";
                    break;
                case "double":
                    $types .= "d";
                    break;
            }
        }
        return $typeString;
    }


    /** Returns results from a query run in the database. Good for SELECT, SHOW, DESCRIBE or EXPLAIN queries.
     * @param string $sql the SQL query to run
     * @param array $parameters (optional) parameters for the query. ? subs for parameters in the query string.
     * @return array the results of the query
     */
    public function query($sql, $parameters=[]) {
        if (!$parameters) {
            // There are no parameters, no possibilities of SQL injection, so just enjoy simple life
            $res = $this->mysqliConnection->query($sql);
        } else {
            $stmt = $this->mysqliConnection->prepare($sql);
            $types = getTypeString($parameters);
            $stmt->bind_param($types, ...$parameters);
            $stmt->execute();
            $res = $stmt->get_result();
            $stmt->close();
        }
        $data = [];
        if (!empty($res)) {
            while ($row = mysqli_fetch_assoc($res)) {
                $data[] = $row;
            }
            mysqli_free_result($res);
            return $data;
        }
        // return new DBResult($data);
    }


    /** Executes SQL statements in the database where results are not returned. Good use cases are INSERT, UPDATE, DELETE, DROP, etc.
     * @param string $sql the SQL statement(s) to execute. Seperate multiple statements with a semicolon.
     * @param array $parameters (optional) parameters for the statement. ? subs for parameters in the statement string.
     */
    public function execute($sql, $parameters=[]) {
        if (!$parameters) {
            $this->mysqliConnection->query($sql);
        } else {
            $stmt = $this->mysqliConnection->prepare($sql);
            $types = getTypeString($parameters);
            $stmt->bind_param($types, ...$parameters);
            $stmt->execute();
        }
    }


    public function close() {
        $this->mysqliConnection->close();
    }

}