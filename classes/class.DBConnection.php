<?php

// class.DBConnection.php

class DBConnection {

    protected static $db;
    private $db_user = 'multiuser';
    private $db_pass = 'EpYy3ERrrNTJSbz4';
    private $db_dsn = "mysql:host=localhost;dbname=tireDB";

    public function __construct() {

        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try {

            self::$db = new PDO($this->db_dsn, $this->db_user, $this->db_pass); //, $options
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            error_log('DBConnection EXCEPTION: ' . $e->getMessage());
        }
    }

    public static function instantiate() {

        if (!self::$db) {
            new DBConnection();
        }

        return self::$db;
    }

}
