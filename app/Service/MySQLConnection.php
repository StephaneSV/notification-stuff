<?php

namespace App\Service;

use \PDO;

/**
 * To be honest, I always worked with a framework so this was kinda new to me.
 * And yes, I took some code from the interweb.
 * The function commented is there as a remember-note when writing repositories.
 */
class MySQLConnection
{
    /**
     * @var PDO
     */
    public PDO $pdo;

    /**
     * @param $db
     * @param $username
     * @param $password
     * @param $host
     * @param $port
     * @param $options
     * @throws \PDOException
     */
    public function __construct(
        $db,
        $username = null,
        $password = null,
        $host = 'database',
        $port = 3306,
        $options = []
    ) {
        $default_options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
        $options = array_replace($default_options, $options);
        $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4";

        $this->pdo = new PDO($dsn, $username, $password, $options);
    }

//    /**
//     * @param $sql
//     * @param $args
//     * @return false|\PDOStatement
//     */
//    public function run($sql, $args = null)
//    {
//        if (!$args) {
//            return $this->pdo->query($sql);
//        }
//        $stmt = $this->pdo->prepare($sql);
//        $stmt->execute($args);
//        return $stmt;
//    }
}