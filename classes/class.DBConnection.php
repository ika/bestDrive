<?php

// class.DBConnection.php

class DBConnection {

	protected static $db;
	protected static $config;

	public function __construct() {

		$options = array(
			PDO::ATTR_PERSISTENT => true,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		);

		try {

			self::$db = new PDO(self::$config->dbDsn, self::$config->dbUser, self::$config->dbPass); //, $options
			self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

		} catch (PDOException $e) {
			error_log('DBConnection EXCEPTION: ' . $e->getMessage());
		}
	}

	public static function instantiate() {

		if (!self::$db) {

			self::$config = require_once(DOCUMENT_ROOT . '/../config.php');

			new DBConnection();
		}

		return self::$db;
	}

}

?>
