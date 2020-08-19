<?php

// class.Import.php

class Import {

    private $db;
    public $data;
    private $uid;

    public function __construct() {

        $this->db = DBConnection::instantiate();
        $this->data = array();
        $this->uid = "{$_SESSION['uid']}";
    }

    public function __destruct() {
        unset($this->db);
    }

    public function importData($data = array()) {

        if ($this->articleExists($data)) {
            $this->editData($data);
        } else {
            $this->addData($data);
        }
    }

    private function articleExists($data = array()) {

        $status = false;

        try {

            $sql = "SELECT COUNT(*) FROM db_tires WHERE ty_article = :article";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':article', $data['article']);
            $stmt->execute();

            $number_of_rows = $stmt->fetchColumn();

            if ($number_of_rows > 0) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('articleExists EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    private function addData($data = array()) {

        $status = false;

        try {

            $onhand = '0';

            $time = time();

            $uid = $this->uid;

            $sql = "INSERT INTO db_tires (
                ty_seg,
                ty_brand,
                ty_inch,
                ty_size,
                ty_li,
                ty_si,
                ty_design,
                ty_article,
                ty_descr,
                ty_ssr,
                ty_net,
                ty_onhand,
                ty_time,
                ty_uid
                ) VALUES (
                :seg,
                :brand,
                :inch,
                :size,
                :li,
                :si,
                :design,
                :article,
                :descr,
                :ssr,
                :net,
                :onhand,
                :time,
                :uid
                )";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':seg', $data['seg']);
            $stmt->bindParam(':brand', $data['brand']);
            $stmt->bindParam(':inch', $data['inch']);
            $stmt->bindParam(':size', $data['size']);
            $stmt->bindParam(':li', $data['li']);
            $stmt->bindParam(':si', $data['si']);
            $stmt->bindParam(':design', $data['design']);
            $stmt->bindParam(':article', $data['article']);
            $stmt->bindParam(':descr', $data['descr']);
            $stmt->bindParam(':ssr', $data['ssr']);
            $stmt->bindParam(':net', $data['net']);
            $stmt->bindParam(':onhand', $onhand);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':uid', $uid);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('addData EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function editData($data = array()) {

        $status = false;

        try {

            $time = time();

            $uid = $this->uid;

            $sql = "UPDATE db_tires SET
                ty_seg = :seg,
                ty_brand = :brand,
                ty_inch = :inch,
                ty_size = :size,
                ty_li = :li,
                ty_si = :si,
                ty_design = :design,
                ty_descr = :descr,
                ty_ssr = :ssr,
                ty_net = :net,
                ty_time = :time,
                ty_uid = :uid
                WHERE ty_article = :article
                 ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':seg', $data['seg']);
            $stmt->bindParam(':brand', $data['brand']);
            $stmt->bindParam(':inch', $data['inch']);
            $stmt->bindParam(':size', $data['size']);
            $stmt->bindParam(':li', $data['li']);
            $stmt->bindParam(':si', $data['si']);
            $stmt->bindParam(':design', $data['design']);
            $stmt->bindParam(':article', $data['article']);
            $stmt->bindParam(':descr', $data['descr']);
            $stmt->bindParam(':ssr', $data['ssr']);
            $stmt->bindParam(':net', $data['net']);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':uid', $uid);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('editData EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

}
