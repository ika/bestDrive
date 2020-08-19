<?php

// class.Config.php

class Config {

    private $db;
    public $data;

    public function __construct() {

        $this->db = DBConnection::instantiate();
        $this->data = array();
    }

    public function __destruct() {
        unset($this->db);
    }

    public function selectConfig() {

        $config = array();

        try {

            $sql = "SELECT
                    a.cn_id As recid,
                    a.cn_markup AS markup,
                    a.cn_version As version,
                    a.cn_software As software,
                    a.cn_domain As domain,
                    a.cn_tzone As tzone,
                    a.cn_upload As upload
                    FROM db_config a";

            $stmt = $this->db->prepare($sql);

            if ($stmt->execute()) {

                $config = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (!empty($config)) {

                    $_SESSION['version'] = "{$config[0]['version']}";
                    $_SESSION['software'] = "{$config[0]['software']}";
                    $_SESSION['markup'] = "{$config[0]['markup']}";
                    $_SESSION['domain'] = "{$config[0]['domain']}";
                    $_SESSION['time_zone'] = "{$config[0]['tzone']}";
                    $mb = (int) "{$config[0]['upload']}";
                    $_SESSION['maxUploadFileSize'] = (1024 * 1024 * $mb); // 32MB

                }
            }
        } catch (PDOException $e) {
            error_log('selectConfig EXCEPTION: ' . $e->getMessage());
        }
        return $config;
    }

    public function updateConfig($data = array()) {

        $status = false;

        if (!empty($data)) {

            try {

                $sql = "UPDATE db_config SET
                        cn_markup=:markup,
                        cn_version=:version,
                        cn_software=:software,
                        cn_domain=:domain,
                        cn_tzone=:tzone,
                        cn_upload=:upload
                        WHERE cn_id=:id";

                $stmt = $this->db->prepare($sql);

                $stmt->bindParam(':id', $data['recid']);
                $stmt->bindParam(':markup', $data['markup']);
                $stmt->bindParam(':version', $data['version']);
                $stmt->bindParam(':software', $data['software']);
                $stmt->bindParam(':domain', $data['domain']);
                $stmt->bindParam(':tzone', $data['tzone']);
                $stmt->bindParam(':upload', $data['upload']);

                if ($stmt->execute()) {
                    $status = true;
                }
            } catch (PDOException $e) {
                error_log('updateConfig EXCEPTION: ' . $e->getMessage());
            }
        }
        return $status;
    }

}

?>
