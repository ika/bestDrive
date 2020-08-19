<?php

// class.Wkspaces.php

class Wkspaces {

    private $db;
    public $data;

    public function __construct() {
        $this->db = DBConnection::instantiate();
        $this->data = array();
    }

    public function __destruct() {
        unset($this->db);
    }

    public function selectWkspaces() {

        $rows = array();

        try {

            $sql = "SELECT 
                        a.ws_id AS recid,
                        a.ws_title AS title,
                        a.ws_wkspace AS wkspace,
                        (SELECT GROUP_CONCAT(DISTINCT b.mo_modname SEPARATOR ', ') 
                        FROM db_modules b WHERE b.mo_wkspace = a.ws_wkspace) AS modules
                        FROM db_wkspaces a
                        ORDER BY a.ws_id DESC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('selectWkspaces EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function addWkspace($data = array()) {

        $status = false;

        try {

            $sql = "INSERT INTO db_wkspaces (ws_title, ws_wkspace)
                                VALUES (:ws_title, :ws_wkspace)";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':ws_title', $data['title']);
            $stmt->bindParam(':ws_wkspace', $data['wkuid']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('addWkspace EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function editWkspace($data = array()) {

        $status = false;

        try {

            $sql = "UPDATE db_wkspaces SET ws_title = :title
                                     WHERE ws_wkspace = :wkspace";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':title', $data['title']);
            $stmt->bindParam(':wkspace', $data['wkspace']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('editWkspace EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function deleteWkspace($data = array()) {

        $status = false;

        try {

            $sql = "DELETE FROM db_wkspaces WHERE ws_wkspace = :wkspace";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':wkspace', $data['wkspace']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('deleteWkspace EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function countWkspaceUsers($data = array()) {

        $count = 0;

        try {

            $sql = "SELECT count(*) AS cnt FROM db_users a WHERE a.us_wkspace = :wkspace";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':wkspace', $data['wkspace']);

            if ($stmt->execute()) {
                $count = $stmt->fetchColumn();
            }
        } catch (PDOException $e) {
            error_log('countWkspaceUsers EXCEPTION: ' . $e->getMessage());
        }

        return $count;
    }

    public function makeWksList() {

        $select = false;

        try {

            $sql = "SELECT 
                        a.ws_title AS title,
                        a.ws_wkspace AS wkspace
                        FROM db_wkspaces a
                        ORDER BY a.ws_id DESC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('makeWksList EXCEPTION: ' . $e->getMessage());
        }

        $select = '[ ';

        foreach ($rows as $row) {
            $t = addslashes($row['title']);
            $select .= "{ id: '{$row['wkspace']}', text: '$t' },";
        }
        
        rtrim($select, ",");

        $select .= ' ]';

        return $select;
    }

}
