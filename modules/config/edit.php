<?php

// edit.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'undefined error';

    if (isset($_POST)) {

        $data = array();

        $data['recid'] = trim($_POST['record']['recid']);
        $data['markup'] = trim($_POST['record']['markup']);
        $data['version'] = trim($_POST['record']['version']);
        $data['software'] = trim($_POST['record']['software']);
        $data['domain'] = trim($_POST['record']['domain']);
        $data['tzone'] = "{$_POST['record']['tzone']}";
        $data['upload'] = trim($_POST['record']['upload']);

        if (empty($data['recid'])) {
            error_log("recid error in config|edit");
        } else {

            $class = new Config();

            if ($class->updateConfig($data)) {
                if ($class->selectConfig()) {
                    $status = 'success';
                }
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
