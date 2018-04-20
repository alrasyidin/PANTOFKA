<?php

namespace model\dao;

abstract class AbstractDao {

    const DB_NAME = "final_project_pantofka";
    const DB_IP = "127.0.0.1";
    const DB_PORT = "3306";
    const DB_USER = "root";
    const DB_PASS = "";

    /* @var $pdo \PDO */
    protected static $pdo;

    protected function __construct(){

    }

    public static function init(){
        try {
            self::$pdo = new \PDO("mysql:host=" . self::DB_IP . ":" . self::DB_PORT . ";dbname=" . self::DB_NAME, self::DB_USER, self::DB_PASS);
            self::$pdo->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION );

        }
        catch (\PDOException $e){
            echo "Problem with db query  - " . $e->getMessage();
        }
    }

    /**
     * @return \PDO
     */
    public static function getPdo()
    {
        return self::$pdo;
    }


}