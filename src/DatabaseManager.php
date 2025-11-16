<?php
namespace App;

class DatabaseManager {
    protected $dbConnection;

    public function __construct(DatabaseConnection $dbConnection) {
        $this->dbConnection = $dbConnection;
    }

    public function fetchData($table) {
        if($table!="users")throw new \Exception("Table not found");

        $query = "SELECT * FROM {$table}";
        return $this->dbConnection->query($query);
    }
}
