<?php

namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

class Database
{
    private static ?PDO $connection = null;

    /**
     * Get PDO connection instance
     * @return PDO
     * @throws RuntimeException if connection fails
     */
    public static function getConnection(): PDO
    {
        // Return existing connection if already initialized
        if (self::$connection !== null) {
            return self::$connection;
        }

        try {
            // Database connection parameters
            $host     = 'localhost';
            $dbName   = 'fullstack';
            $username = 'scandi';
            $password = 'Password123!';

            // Create new PDO connection
            self::$connection = new PDO(
                "mysql:host={$host};dbname={$dbName};charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // Throw exceptions on errors
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Return results as associative arrays
                    PDO::ATTR_EMULATE_PREPARES   => false,                   // Use real prepared statements
                ]
            );
        } catch (PDOException $e) {
            // Throw runtime exception if connection fails
            throw new RuntimeException('Database connection failed: ' . $e->getMessage());
        }

        return self::$connection;
    }
}