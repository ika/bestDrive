<?php

// class.Login.php

class Login extends Modules {

    private $db;
    public $data;

    public function __construct() {

        $this->db = DBConnection::instantiate();
        $this->data = array();
    }

    public function __destruct() {
        unset($this->db);
    }

    public function Authenticate($data = array()) {

        try {

            $sql = "SELECT
                    a.us_id AS recid,
                    a.us_uid AS auid,
                    a.us_email AS email,
                    a.us_pass AS password,
                    a.us_name AS firstname,
                    a.us_surname AS lastname,
                    a.us_tel AS telephone,
                    a.us_active AS active,
                    a.us_uid AS userid,
                    a.us_wkspace AS wkspace,
                    a.us_start AS start
                    FROM db_users a
                    WHERE a.us_email = :email";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $data['email']);

            if ($stmt->execute()) {
                $this->data = $stmt->fetch(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('Authenticate EXCEPTION: ' . $e->getMessage());
        }
    }

    public function lastLogin() {

        $status = false;

        try {

            $time = time();

            $sql = "UPDATE db_users SET us_time = :time WHERE us_uid = :uid";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':uid', $this->data['auid']);
            $stmt->bindParam(':time', $time);
            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('lastLogin EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

}
