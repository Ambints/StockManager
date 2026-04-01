<?php

// ── Paramètres de connexion ───────────────────────────────
define('DB_HOST',   'localhost');
define('DB_PORT',   '5432');
define('DB_NAME',   'stock_boutique');
define('DB_USER',   'postgres');
define('DB_PASS',   '2007');   
define('DB_CHARSET','utf8');

class Database
{
    private static ?PDO $instance = null;

    /**
     * Retourne l'instance unique de PDO
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                DB_HOST, DB_PORT, DB_NAME
            );

            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);

                // Fuseau horaire PostgreSQL
                self::$instance->exec("SET TIME ZONE 'Indian/Antananarivo'");

            } catch (PDOException $e) {
                if (APP_DEBUG) {
                    die('<pre>Erreur de connexion PDO : ' . $e->getMessage() . '</pre>');
                }
                die('Erreur de connexion à la base de données. Vérifiez la configuration.');
            }
        }

        return self::$instance;
    }

    /** Empêche l'instanciation et la copie */
    private function __construct() {}
    private function __clone()    {}
}
