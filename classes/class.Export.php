<?php

// class.Import.php

class Export {

    private $db;
    public $data;

    public function __construct() {
        $this->db = DBConnection::instantiate();
        $this->data = array();
    }

    public function __destruct() {
        unset($this->db);
    }

    public function exportTyres() {

        try {

            $sql = "SELECT
                a.ty_seg AS seg,
                a.ty_brand AS brand,
                a.ty_inch AS inch,
                a.ty_size AS size,
                a.ty_li AS li,
                a.ty_si AS si,
                a.ty_design AS design,
                a.ty_article AS article,
                a.ty_descr AS descr,
                a.ty_ssr AS ssr,
                a.ty_rrp As rrp,
                a.ty_net AS net,
                a.ty_onhand AS onhand
                FROM db_tires a
                ORDER BY a.ty_inch ASC, a.ty_id DESC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $this->data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('exportTyres EXCEPTION: ' . $e->getMessage());
        }

    }

}

?>
