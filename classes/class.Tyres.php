<?php

// class.Tyres.php

class Tyres {

    private $db;
    public $data;
    private $uid;

    //private $cat;

    public function __construct() {
        $this->db = DBConnection::instantiate();
        $this->data = array();
        $this->uid = "{$_SESSION['uid']}";
    }

    public function __destruct() {
        unset($this->db);
    }

    public function selectTyres() {

        $rows = array();

        try {

            $sql = "SELECT
                a.ty_id AS recid,
                a.ty_seg AS seg,
                a.ty_place As place,
                a.ty_brand AS brand,
                a.ty_inch AS inch,
                a.ty_size AS size,
                CONCAT(a.ty_li,' ',a.ty_si) AS lisi,
                a.ty_li AS li,
                a.ty_si AS si,
                a.ty_design AS design,
                a.ty_article AS article,
                a.ty_descr AS descr,
                a.ty_ssr AS ssr,
                a.ty_net AS net,
                a.ty_onhand AS onhand
                FROM db_tires a
                ORDER BY a.ty_inch ASC, a.ty_id DESC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('selectTyres EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function genMd5ID($len = 6) {
        return substr(md5(rand() . rand()), 0, $len);
    }

    public function addTyre($data = array()) {

        $status = false;

        try {

            //$tid = 'GM-' . $this->genMd5ID();

            $uid = $this->uid;

            $time = time();

            $sql = "INSERT INTO db_tires (
                ty_seg,
                ty_place,
                ty_brand,
                ty_inch,
                ty_size,
                ty_li,
                ty_si,
                ty_design,
                ty_ssr,
                ty_article,
                ty_net,
                ty_onhand,
                ty_time,
                ty_uid
                ) VALUES (
                :seg,
                :place,
                :brand,
                :inch,
                :size,
                :li,
                :si,
                :design,
                :ssr,
                :article,
                :net,
                :onhand,
                :time,
                :uid
                )";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':seg', $data['seg']);
            $stmt->bindParam(':place', $data['place']);
            $stmt->bindParam(':brand', $data['brand']);
            $stmt->bindParam(':inch', $data['rim']);
            $stmt->bindParam(':size', $data['size']);
            $stmt->bindParam(':li', $data['li']);
            $stmt->bindParam(':si', $data['si']);
            $stmt->bindParam(':design', $data['design']);
            $stmt->bindParam(':ssr', $data['ssr']);
            $stmt->bindParam(':article', $data['id']);
            $stmt->bindParam(':net', $data['cost']);
            $stmt->bindParam(':onhand', $data['onhand']);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':uid', $uid);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('addTyre EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function editTyre($data = array()) {

        $status = false;

        try {

            $time = time();

            // $tid = 'GM-' . substr($time, 4);

            $uid = $this->uid;

            $sql = "UPDATE db_tires SET
                ty_seg = :seg,
                ty_place = :place,
                ty_brand = :brand,
                ty_inch = :inch,
                ty_size = :size,
                ty_li = :li,
                ty_si = :si,
                ty_design = :design,
                ty_ssr = :ssr,
                ty_article = :article,
                ty_net = :net,
                ty_onhand = :onhand,
                ty_time = :time,
                ty_uid = :uid
                WHERE ty_id = :recid
                 ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':recid', $data['recid']);
            $stmt->bindParam(':seg', $data['seg']);
            $stmt->bindParam(':place', $data['place']);
            $stmt->bindParam(':brand', $data['brand']);
            $stmt->bindParam(':inch', $data['rim']);
            $stmt->bindParam(':size', $data['size']);
            $stmt->bindParam(':design', $data['design']);
            $stmt->bindParam(':ssr', $data['ssr']);
            $stmt->bindParam(':article', $data['id']);
            $stmt->bindParam(':li', $data['li']);
            $stmt->bindParam(':si', $data['si']);
            $stmt->bindParam(':net', $data['cost']);
            $stmt->bindParam(':onhand', $data['onhand']);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':uid', $uid);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('editTyre EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function updateOnhand($data = array()) {

        $status = false;

        try {

            $time = time();

            $uid = $this->uid;

            $sql = "UPDATE db_tires SET
                ty_onhand = :onhand,
                ty_time = :time,
                ty_uid = :uid
                WHERE ty_id = :recid
                 ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':recid', $data['recid']);
            $stmt->bindParam(':onhand', $data['onhand']);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':uid', $uid);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('updateOnhand EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function updatePrices($data = array()) {

        $status = false;

        try {

            $time = time();

            $uid = $this->uid;

            $sql = "UPDATE db_tires SET
                ty_net = :net,
                ty_time = :time,
                ty_uid = :uid
                WHERE ty_id = :recid
                 ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':recid', $data['recid']);
            $stmt->bindParam(':net', $data['amount']);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':uid', $uid);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('updatePrices EXCEPTION: ' . $e->getMessage());
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
