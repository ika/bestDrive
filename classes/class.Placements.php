<?php

// class.Placements.php

class Placements {

    private $db;
    public $data;

    public function __construct() {
        $this->db = DBConnection::instantiate();
        $this->data = array();
    }

    public function __destruct() {
        unset($this->db);
    }

    public function placeSelector() {

        try {

            $sql = "SELECT
                a.pl_name AS name,
                a.pl_descr AS descr
                FROM db_placements a";

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
            error_log('placeSelector EXCEPTION: ' . $e->getMessage());
        }

        return $select;
    }

    public function returnPlaces() {

        try {

            $sql = "SELECT
                a.pl_id AS recid,
                a.pl_name AS name,
                a.pl_descr AS descr
                FROM db_placements a";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('returnPlaces EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function addPlace($data = array()) {

        $status = false;

        try {

            $sql = "INSERT INTO db_placements (pl_name, pl_descr)
                                VALUES (:name, :descr)";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':descr', $data['descr']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('addPlace EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function editPlace($data = array()) {

        $status = false;

        try {

            $sql = "UPDATE db_placements SET pl_name = :name, pl_descr = :descr
                                     WHERE pl_id = :recid";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':recid', $data['recid']);
            $stmt->bindParam(':name', $data['name']);
            $stmt->bindParam(':descr', $data['descr']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('editPlace EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function removePlace($data = array()) {

        $status = false;

        try {

            $sql = "DELETE FROM db_placements WHERE pl_id = :recid";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':recid', $data['recid']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('removePlace EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

}
