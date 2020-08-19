<?php

// class.Printing.php

class Printing {

    private $db;
    public $data;

    public function __construct() {
        $this->db = DBConnection::instantiate();
        $this->data = array();
    }

    public function __destruct() {
        unset($this->db);
    }

    // public function printAllTyres($inch = 0) {
    public function printAllTyres() {

        $rows = array();

        //if ($inch != 0) {
        //$inch = 14;
//$sqll = "SELECT
//a.ty_brand AS brand,
//a.ty_inch AS inch,
//a.ty_size AS size,
//CONCAT(a.ty_li,' ',a.ty_si) AS lisi,
//(CASE WHEN a.ty_ssr IS NULL THEN a.ty_design ELSE CONCAT(a.ty_design,' ',a.ty_ssr) END) AS design,
//a.ty_article AS article,
//a.ty_onhand AS onhand
//FROM db_tires a
//WHERE a.ty_inch LIKE '[>inch<]%'
//AND a.ty_seg NOT LIKE 'Trac%' 
//AND a.ty_seg NOT LIKE 'BMW%' 
//AND a.ty_seg NOT LIKE 'Mach%' 
//AND a.ty_onhand > 0
//ORDER BY a.ty_inch ASC, a.ty_id DESC";

        try {

            $sql = "SELECT
                a.ty_brand AS brand,
                a.ty_inch AS inch,
                a.ty_size AS size,
                CONCAT(a.ty_li,' ',a.ty_si) AS lisi,
                (CASE WHEN a.ty_ssr IS NULL THEN a.ty_design ELSE CONCAT(a.ty_design,' ',a.ty_ssr) END) AS design,
                a.ty_article AS article,
                a.ty_onhand AS onhand
                FROM db_tires a
                WHERE a.ty_onhand > 0
                ORDER BY a.ty_inch ASC, a.ty_size ASC";

            //$sql = str_replace("[>inch<]", $inch, $sqll);

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('printAllTyres EXCEPTION: ' . $e->getMessage());
        }
        //}

        return $rows;
    }
    
     public function printAllSpares() {

        $rows = array();

        try {

            $sql = "SELECT * FROM db_spares a WHERE a.sp_onhand > 0 ORDER BY a.sp_id ASC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('printAllSpares EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function printAdditionalTyres() {

        $rows = array();

        try {

            $sql = "SELECT
                a.ty_brand AS brand,
                a.ty_inch AS inch,
                a.ty_size AS size,
                CONCAT(a.ty_li,' ',a.ty_si) AS lisi,
                (CASE WHEN a.ty_ssr IS NULL THEN a.ty_design ELSE CONCAT(a.ty_design,' ',a.ty_ssr) END) AS design,
                a.ty_article AS article,
                a.ty_onhand AS onhand
                FROM db_tyres a
                WHERE ty_onhand > 0 AND ty_article LIKE 'MY%' AND ty_seg NOT LIKE 'Trac%' OR ty_seg NOT LIKE 'BMW%' OR ty_seg NOT LIKE 'Mach%'
                ORDER BY a.ty_inch ASC, a.ty_id DESC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('printTyres EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function printTractorTyres() {

        $rows = array();

        try {

            $sql = "SELECT
                a.ty_brand AS brand,
                a.ty_inch AS inch,
                a.ty_size AS size,
                CONCAT(a.ty_li,' ',a.ty_si) AS lisi,
                (CASE WHEN a.ty_ssr IS NULL THEN a.ty_design ELSE CONCAT(a.ty_design,' ',a.ty_ssr) END) AS design,
                a.ty_article AS article,
                a.ty_onhand AS onhand
                FROM db_tyres a
                WHERE ty_seg LIKE 'Trac%'
                ORDER BY a.ty_inch ASC, a.ty_id DESC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('printTyres EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function printBMWTyres() {

        $rows = array();

        try {

            $sql = "SELECT
                a.ty_brand AS brand,
                a.ty_inch AS inch,
                a.ty_size AS size,
                CONCAT(a.ty_li,' ',a.ty_si) AS lisi,
                (CASE WHEN a.ty_ssr IS NULL THEN a.ty_design ELSE CONCAT(a.ty_design,' ',a.ty_ssr) END) AS design,
                a.ty_article AS article,
                a.ty_onhand AS onhand
                FROM db_tyres a
                WHERE ty_seg LIKE 'BMW%'
                ORDER BY a.ty_inch ASC, a.ty_id DESC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('printTyres EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function printMachineTyres() {

        $rows = array();

        try {

            $sql = "SELECT
                a.ty_brand AS brand,
                a.ty_inch AS inch,
                a.ty_size AS size,
                CONCAT(a.ty_li,' ',a.ty_si) AS lisi,
                (CASE WHEN a.ty_ssr IS NULL THEN a.ty_design ELSE CONCAT(a.ty_design,' ',a.ty_ssr) END) AS design,
                a.ty_article AS article,
                a.ty_onhand AS onhand
                FROM db_tyres a
                WHERE ty_seg LIKE 'Mach%'
                ORDER BY a.ty_inch ASC, a.ty_id DESC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('printTyres EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

}
