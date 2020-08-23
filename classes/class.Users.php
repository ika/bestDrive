<?php

// class.Users.php

class Users {

    private $db;
    public $data;

    public function __construct() {
        $this->db = DBConnection::instantiate();
        $this->data = array();
    }

    public function __destruct() {
        unset($this->db);
    }

    public function selectUsers() {

        $rows = array();
        $yes = 'yes';
        $no = 'no';

        try {

            $sql = "SELECT
                a.us_id AS recid,
                a.us_name AS firstname,
                a.us_surname AS lastname,
                CONCAT(a.us_name, ' ', a.us_surname) AS fullname,
                a.us_email AS email,
                FROM_BASE64(a.us_pass) AS password,
                a.us_tel AS telephone,
                a.us_active AS active,
                a.us_notes AS notes,
                a.us_uid AS userid,
                a.us_uid,
                a.us_start AS start,
                b.ws_title AS wkspace,
                b.ws_wkspace AS wksid,
                (CASE WHEN a.us_time = '0' THEN 'N/A' ELSE FROM_UNIXTIME(a.us_time, '%D %M %Y: %H:%i') END) AS lastlogin
                FROM db_users a
                JOIN db_wkspaces b ON ( b.ws_wkspace = a.us_wkspace)
                WHERE a.us_active = :yes OR a.us_active = :no
                ORDER BY a.us_time DESC";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':yes', $yes);
            $stmt->bindParam(':no', $no);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('select EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function addUser($data = array()) {

        $status = false;

        try {

            $time = '0';

            $uid = uniqid();

            //$ielement = base64_encode(file_get_contents('lib/images/user.png'));

            $password = base64_encode($data['password']);

            $sql = "INSERT INTO db_users (us_name, us_surname, us_pass, us_email, us_tel, us_active, us_uid, us_time, us_wkspace, us_start, us_notes)
                    VALUES (:us_name, :us_surname, :us_pass, :us_email, :us_tel, :us_active, :us_uid, :us_time, :us_wkspace, :us_start, :us_notes)";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':us_name', $data['firstname']);
            $stmt->bindParam(':us_surname', $data['lastname']);
            $stmt->bindParam(':us_pass', $password);
            $stmt->bindParam(':us_email', $data['email']);
            $stmt->bindParam(':us_tel', $data['telephone']);
            $stmt->bindParam(':us_active', $data['active']);
            $stmt->bindParam(':us_notes', $data['notes']);
            $stmt->bindParam(':us_uid', $uid);
            $stmt->bindParam(':us_time', $time);
            $stmt->bindParam(':us_wkspace', $data['wkspaces']);
            $stmt->bindParam(':us_start', $data['start']);

            if ($stmt->execute()) {
                //$this->data['lastid'] = $this->db->lastInsertId();
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('addUser EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

//    public function delUserCheck($data = array()) {
//
//        $status = 'NO';
//
//        try {
//
//            $sql = "SELECT a.us_time AS time FROM db_users a WHERE a.us_id = :recid AND a.us_uid = :userid";
//
//            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(':recid', $data['recid']);
//            $stmt->bindParam(':userid', $data['userid']);
//
//            if ($stmt->execute()) {
//                $status = $stmt->fetch(PDO::FETCH_ASSOC); //  YES / NO
//            }
//        } catch (PDOException $e) {
//            error_log('deletUserCheck EXCEPTION: ' . $e->getMessage());
//        }
//
//        return $status;
//    }

    public function deleteUser($data = array()) {

        $status = false;
        $del = 'del';

        try {

            $sql = "UPDATE db_users SET us_active = :del WHERE us_id = :recid";

            //$sql = "DELETE FROM db_users WHERE us_id = :recid";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':recid', $data['recid']);
            $stmt->bindParam(':del', $del);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('deleteUser EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function checkEmailExists($data = array()) {

        $status = false;

        try {

            $sql = "SELECT a.us_id AS ID FROM db_users a WHERE us_email = :email";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $data['email']);

            $stmt->execute();
            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($exists['ID'])) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('checkEmailExists EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

//    public function checkEmailExists($data = array()) {
//
//        $count = 0;
//
//        try {
//
//            $sql = "SELECT count(*) AS cnt FROM db_users a WHERE a.us_email = :email";
//
//            $stmt = $this->db->prepare($sql);
//            $stmt->bindParam(':email', $data['email']);
//
//            if ($stmt->execute()) {
//                $count = $stmt->fetchColumn();
//            }
//        } catch (PDOException $e) {
//            error_log('checkEmailExists EXCEPTION: ' . $e->getMessage());
//        }
//
//        return $count;
//    }

    public function checkOtherEmailExists($data = array()) {

        $status = false;

        try {

            $sql = "SELECT us_id AS ID FROM db_users WHERE us_email = :email AND us_id != :recid";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':email', $data['email']);
            $stmt->bindParam(':recid', $data['recid']);

            $stmt->execute();

            $exists = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($exists['ID'])) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('checkOtherEmailExists EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function editUser($data = array()) {

        $status = false;

        try {

            $password = base64_encode($data['password']);

            $sql = "UPDATE db_users SET
                us_name = :us_name,
                us_surname = :us_surname,
                us_pass = :us_pass,
                us_email = :us_email,
                us_tel = :us_tel,
                us_notes = :us_notes,
                us_active = :us_active,
                us_wkspace = :us_wkspace,
                us_start = :us_start
                 WHERE us_id = :recid
                 ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':recid', $data['recid']);
            $stmt->bindParam(':us_name', $data['firstname']);
            $stmt->bindParam(':us_surname', $data['lastname']);
            $stmt->bindParam(':us_pass', $password);
            $stmt->bindParam(':us_email', $data['email']);
            $stmt->bindParam(':us_tel', $data['telephone']);
            $stmt->bindParam(':us_active', $data['active']);
            $stmt->bindParam(':us_notes', $data['notes']);
            $stmt->bindParam(':us_wkspace', $data['wkspaces']);
            $stmt->bindParam(':us_start', $data['start']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('editUser EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function getLastModifiedUser($data = array()) {

        $rows = array();

        try {

            $sql = "SELECT CONCAT(b.us_name,' ',b.us_surname) AS name
                    FROM db_tires a,db_users b
                    WHERE a.ty_uid = b.us_uid
                    AND ty_id = :recid";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':recid', $data['recid']);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('getLastModifiedUser EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

}

?>
