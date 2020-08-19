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
class Hydrolics {

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

	public function selectAllHydrolics() {

		$rows = array();
		try {

			$sql = "SELECT
				a.hy_id AS recid,
				a.hy_partid As partid,
				a.hy_name AS partname,
				a.hy_date AS date,
				a.hy_size AS size,
				a.hy_cost AS cost,
				a.hy_onhand AS onhand,
				a.hy_descr AS descr,
				a.hy_uid AS uid,
				a.hy_time AS time
				FROM db_hydrolics a
				ORDER BY a.hy_time DESC";

			$stmt = $this->db->prepare($sql);

			if ($stmt->execute()) {
				$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
			}
		} catch (PDOException $e) {
			error_log('selectAllHydrolics EXCEPTION: ' . $e->getMessage());
		}

		return $rows;
	}

	public function addHydrolics($data = array()) {

		$status = false;

		try {

			$uid = $this->uid;

			$time = time();

			$sql = "INSERT INTO db_hydrolics (
				hy_partid,
				hy_name,
				hy_date,
				hy_size,
				hy_cost,
				hy_onhand,
				hy_descr,
				hy_uid,
				hy_time
		) VALUES (
			:hy_partid,
			:hy_name,
			:hy_date,
			:hy_size,
			:hy_cost,
			:hy_onhand,
			:hy_descr,
			:hy_uid,
			:hy_time
		)";

			$stmt = $this->db->prepare($sql);

			$stmt->bindParam(':hy_partid', $data['partid']);
			$stmt->bindParam(':hy_name', $data['partname']);
			$stmt->bindParam(':hy_date', $data['date']);
			$stmt->bindParam(':hy_size', $data['size']);
			$stmt->bindParam(':hy_cost', $data['cost']);
			$stmt->bindParam(':hy_onhand', $data['onhand']);
			$stmt->bindParam(':hy_descr', $data['descr']);
			$stmt->bindParam(':hy_uid', $uid);
			$stmt->bindParam(':hy_time', $time);

			if ($stmt->execute()) {
				$status = true;
			}
		} catch (PDOException $e) {
			error_log('addHydrolics EXCEPTION: ' . $e->getMessage());
		}

		return $status;
	}

	public function updatePrices($data = array()) {

		$status = false;

		try {

			$time = time();

			$uid = $this->uid;

			$sql = "UPDATE db_hydrolics SET
				hy_cost = :cost,
				hy_time = :time,
				hy_uid = :uid
				WHERE hy_id = :recid
";

			$stmt = $this->db->prepare($sql);

			$stmt->bindParam(':recid', $data['recid']);
			$stmt->bindParam(':cost', $data['cost']);
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

			$sql = "UPDATE db_hydrolics SET
				hy_onhand = :onhand,
				hy_time = :time,
				hy_uid = :uid
				WHERE hy_id = :recid
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
				FROM db_hydrolics a, db_users b
				WHERE a.hy_uid = b.us_uid
				AND a.hy_id = :recid";

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

	public function editHydrolics($data = array()) {

		try {

			$time = time();

			$uid = $this->uid;

			$sql = "UPDATE db_hydrolics SET
				hy_name = :name,
				hy_date = :date,
				hy_size = :size,
				hy_cost = :cost,
				hy_onhand = :onhand,
				hy_descr = :descr,
				hy_uid = :uid,
				hy_time = :time
				WHERE hy_id = :recid";

			$stmt = $this->db->prepare($sql);

			$stmt->bindParam(':recid', $data['recid']);
			$stmt->bindParam(':name', $data['partname']);
			$stmt->bindParam(':date', $data['date']);
			$stmt->bindParam(':size', $data['size']);
			$stmt->bindParam(':cost', $data['cost']);
			$stmt->bindParam(':onhand', $data['onhand']);
			$stmt->bindParam(':descr', $data['descr']);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':time', $time);

			if ($stmt->execute()) {
				$status = true;
			}
		} catch (PDOException $e) {
			error_log('editHydrolics EXCEPTION: ' . $e->getMessage());
		}

		return $status;
	}

	public function duplicatePartNumber($data = array()) {

		$count = 0;

		if (!empty($data['partname'])) {

			$sql = "SELECT count(*) AS cnt FROM db_hydrolics WHERE hy_name = :partname";

			try {

				$stmt = $this->db->prepare($sql);

				$stmt->bindParam(':partname', $data['partname']);

				if ($stmt->execute()) {
					$count = $stmt->fetchColumn();
				}
			} catch (PDOException $e) {
				error_log('duplicatePartNumber EXCEPTION: ' . $e->getMessage());
			}
		}

		return $count;
	}

}
