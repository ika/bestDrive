<?php

// class.Catagories.php

class Segments {

    private $db;
    public $data;

    public function __construct() {
        $this->db = DBConnection::instantiate();
        $this->data = array();
    }

    public function __destruct() {
        unset($this->db);
    }

    public function segSelector() {

        try {

            $sql = "SELECT
                a.ca_name AS name,
                a.ca_descr AS descr
                FROM db_segments a";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {

                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $select = '[ ';

                foreach ($rows as $row) {
                    $select .= "{ id: '{$row['name']}', text: '{$row['descr']}' }, ";
                }

                $select .= ' ]';
            }
        } catch (PDOException $e) {
            error_log('segSelector EXCEPTION: ' . $e->getMessage());
        }

        return $select;
    }

    public function returnSegs() {

        try {

            $sql = "SELECT
                a.ca_id AS recid,
                a.ca_name AS name,
                a.ca_descr AS descr
                FROM db_segments a";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('returnCatagories EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function addSeg($data = array()) {

        $status = false;

        try {

            $sql = "INSERT INTO db_segments (ca_name, ca_descr)
                                VALUES (:name, :descr)";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':descr', $data['descr']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('addCat EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function editSeg($data = array()) {

        $status = false;

        try {

            $sql = "UPDATE db_segments SET ca_name = :name, ca_descr = :descr
                                     WHERE ca_id = :recid";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':recid', $data['recid']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':descr', $data['descr']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('editSeg EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function removeSeg($data = array()) {

        $status = false;

        try {

            $sql = "DELETE FROM db_segments WHERE ca_id = :recid";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':recid', $data['recid']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('removeCat EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

}
