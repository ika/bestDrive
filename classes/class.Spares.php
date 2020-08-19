<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class
 *
 * @author ika
 */
class Spares {

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

    public function selectAllSpares() {

        $rows = array();

        try {

            $sql = "SELECT
                a.sp_id AS recid,
                a.sp_partid As partid,
                a.sp_partno AS partnumber,
                a.sp_suppinv AS suppinvoice,
                a.sp_datein AS datein,
                a.sp_jobcard AS jobcard,
                a.sp_cost AS cost,
                a.sp_onhand AS onhand,
                a.sp_descr AS description
                FROM db_spares a
                ORDER BY a.sp_time DESC";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            error_log('selectAllSpares EXCEPTION: ' . $e->getMessage());
        }

        return $rows;
    }

    public function addSpares($data = array()) {

        $status = false;

        try {

            $uid = $this->uid;

            $time = time();

            $sql = "INSERT INTO db_spares (
                sp_partid,
                sp_partno,
                sp_suppinv,
                sp_datein,
                sp_jobcard,
                sp_cost,
                sp_onhand,
                sp_descr,
                sp_uid,
                sp_time
                ) VALUES (
                :sp_partid,
                :sp_partno,
                :sp_suppinv,
                :sp_datein,
                :sp_jobcard,
                :sp_cost,
                :sp_onhand,
                :sp_descr,
                :sp_uid,
                :sp_time
                )";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':sp_partid', $data['partid']);
            $stmt->bindParam(':sp_partno', $data['partnumber']);
            $stmt->bindParam(':sp_suppinv', $data['suppinvoice']);
            $stmt->bindParam(':sp_datein', $data['datein']);
            $stmt->bindParam(':sp_jobcard', $data['jobcard']);
            $stmt->bindParam(':sp_cost', $data['cost']);
            $stmt->bindParam(':sp_onhand', $data['onhand']);
            $stmt->bindParam(':sp_descr', $data['description']);
            $stmt->bindParam(':sp_uid', $uid);
            $stmt->bindParam(':sp_time', $time);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('addSpares EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function updatePrices($data = array()) {

        $status = false;

        try {

            $time = time();

            $uid = $this->uid;

            $sql = "UPDATE db_spares SET
                sp_cost = :net,
                sp_time = :time,
                sp_uid = :uid
                WHERE sp_id = :recid
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

    public function updateOnhand($data = array()) {

        $status = false;

        try {

            $time = time();

            $uid = $this->uid;

            $sql = "UPDATE db_spares SET
                sp_onhand = :onhand,
                sp_time = :time,
                sp_uid = :uid
                WHERE sp_id = :recid
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

    public function getLastModifiedUser($data = array()) {

        $rows = array();

        try {

            $sql = "SELECT CONCAT(b.us_name,' ',b.us_surname) AS name
                    FROM db_spares a, db_users b
                    WHERE a.sp_uid = b.us_uid
                    AND a.sp_id = :recid";

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

    public function editSpares($data = array()) {

        $status = false;

        $data['recid'] = "{$_POST['record']['recid']}";
        $data['partid'] = trim($_POST['record']['partid']);
        $data['partnumber'] = "{$_POST['record']['partnumber']}";
        $data['suppinvoice'] = "{$_POST['record']['suppinvoice']}";
        $data['datein'] = trim($_POST['record']['datein']);
        $data['jobcard'] = trim($_POST['record']['jobcard']);
        $data['description'] = trim($_POST['record']['description']);
        $data['cost'] = trim($_POST['record']['cost']);
        $data['onhand'] = trim($_POST['record']['onhand']);

        try {

            $time = time();

            $uid = $this->uid;

            $sql = "UPDATE db_spares SET
                sp_partid = :partid,
                sp_partno = :partnumber,
                sp_suppinv = :suppinvoice,
                sp_datein = :datein,
                sp_jobcard = :jobcard,
                sp_cost = :cost,
                sp_onhand = :onhand,
                sp_descr = :description,
                sp_uid = :uid,
                sp_time = :time
                WHERE sp_id = :recid
                 ";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':recid', $data['recid']);
            $stmt->bindParam(':partid', $data['partid']);
            $stmt->bindParam(':partnumber', $data['partnumber']);
            $stmt->bindParam(':suppinvoice', $data['suppinvoice']);
            $stmt->bindParam(':datein', $data['datein']);
            $stmt->bindParam(':jobcard', $data['jobcard']);
            $stmt->bindParam(':cost', $data['cost']);
            $stmt->bindParam(':onhand', $data['onhand']);
            $stmt->bindParam(':description', $data['description']);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':uid', $uid);

            if ($stmt->execute()) {
                $status = true;
            }
        } catch (PDOException $e) {
            error_log('editSpares EXCEPTION: ' . $e->getMessage());
        }

        return $status;
    }

    public function duplicatePartNumber($data = array()) {

        $count = 0;

        if (!empty($data['partnumber'])) {

            $sql = "SELECT count(*) AS cnt FROM db_spares WHERE sp_partno = :partnumber";

            try {

                $stmt = $this->db->prepare($sql);

                $stmt->bindParam(':partnumber', $data['partnumber']);

                if ($stmt->execute()) {
                    $count = $stmt->fetchColumn();
                }
            } catch (PDOException $e) {
                error_log('duplicatePartNumber EXCEPTION: ' . $e->getMessage());
            }
        }

        return $count;
    }

//    public function correctError($data = array()) {
//
//        $status = false;
//
//        try {
//
//            $sql = "UPDATE db_spares SET
//                sp_time = :time,
//                sp_uid = :uid
//                WHERE sp_id = :recid
//                 ";
//
//            $stmt = $this->db->prepare($sql);
//
//            $stmt->bindParam(':recid', $data['recid']);
//            $stmt->bindParam(':time', $data['time']);
//            $stmt->bindParam(':uid', $data['uid']);
//
//            if ($stmt->execute()) {
//                $status = true;
//            }
//        } catch (PDOException $e) {
//            error_log('correctError EXCEPTION: ' . $e->getMessage());
//        }
//
//        return $status;
//    }

}
