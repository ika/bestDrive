<?php

// class.Modules.php

class Modules {

    private $db;
    public $data;
    public $workspace;

    public function __construct() {
        $this->db = DBConnection::instantiate();
        $this->data = array();
        $this->workspace = "{$_SESSION['wkspace']}";
    }

    public function __destruct() {
        unset($this->db);
    }

    public function returnModules() {

        try {

            $sql = "SELECT
                a.mo_modname AS name
                FROM db_modules a
                WHERE a.mo_wkspace = :wkspace";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':wkspace', $this->workspace);

            if ($stmt->execute()) {
                $mods = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($mods as $mod) {
                    $this->data['modules'][] = $mod['name'];
                }
            }
        } catch (PDOException $e) {
            error_log('returnModules EXCEPTION: ' . $e->getMessage());
        }

        return $this->data['modules'];
    }

    public function addMod($data = array()) {

        $status = false;

        try {

            $sql = "INSERT INTO db_modules (mo_modname, mo_wkspace)
                                VALUES (:modid, :wkspace)";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':modid', $data['modid']);
            $stmt->bindParam(':wkspace', $data['wkspace']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('addMod EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function removeMod($data = array()) {

        $status = false;

        try {

            $sql = "DELETE FROM db_modules WHERE mo_modname = :modid AND mo_wkspace = :wkspace";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':modid', $data['modid']);
            $stmt->bindParam(':wkspace', $data['wkspace']);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('deleteMod EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

}
