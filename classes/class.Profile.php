<?php

// class.Profile.php

class Profile {

	private $db;
	public $data;

	public function __construct() {
		$this->db = DBConnection::instantiate();
		$this->data = array();
		$this->uid = "{$_SESSION['uid']}";
	}

	public function __destruct() {
		unset($this->db);
	}

	public function getProfile() {

		$row = array();

		try {

			$sql = "SELECT
				a.us_id AS recid,
				a.us_name AS firstname,
				a.us_surname AS lastname,
				a.us_email AS email,
				FROM_BASE64(a.us_pass) AS password,
				a.us_tel AS telephone,
				a.us_notes AS notes
				FROM db_users a
				WHERE a.us_uid = :uid";

			$stmt = $this->db->prepare($sql);

			$stmt->bindParam(':uid', $this->uid);

			if ($stmt->execute()) {
				$row = $stmt->fetch(PDO::FETCH_ASSOC);
			}
		} catch (PDOException $e) {
			error_log('getProfile EXCEPTION: ' . $e->getMessage());
		}

		return $row;
	}

	public function updateProfile($data = array()) {

		$status = false;

		try {

			$password = base64_encode($data['password']);

			$sql = "UPDATE db_users SET
				us_name = :us_name,
				us_surname = :us_surname,
				us_pass = :us_pass,
				us_email = :us_email,
				us_tel = :us_tel,
				us_notes = :us_notes
				WHERE us_id = :recid";

			$stmt = $this->db->prepare($sql);

			$stmt->bindParam(':recid', $data['recid']);
			$stmt->bindParam(':us_name', $data['firstname']);
			$stmt->bindParam(':us_surname', $data['lastname']);
			$stmt->bindParam(':us_pass', $password);
			$stmt->bindParam(':us_email', $data['email']);
			$stmt->bindParam(':us_tel', $data['telephone']);
			$stmt->bindParam(':us_notes', $data['notes']);

			if ($stmt->execute()) {
				$status = true;
			}
		} catch (PDOException $e) {
			error_log('updateProfile EXCEPTION: ' . $e->getMessage());
		}

		return $status;
	}

	public function checkOtherEmailExists($data = array()) {

		$status = false;

		try {

			$sql = "SELECT us_id AS ID FROM db_users WHERE us_email = :email AND us_id != :recid";

			$stmt = $this->db->prepare($sql);
			$stmt->bindParam(':email', $data['email']);
			$stmt->bindParam(':recid', $data['recid']);

			$stmt->execute();

			$exists = $stmt->fetch(PDO::FETCH_ASSOC);

			if (!empty($exists['ID'])) {
				$status = true;
			}
		} catch (PDOException $e) {
			error_log('checkOtherEmailExists EXCEPTION: ' . $e->getMessage());
		}

		return $status;
	}

}

?>
