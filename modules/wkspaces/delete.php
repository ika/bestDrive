<?php

// wkspaces|delete.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'undefined error';

    if (isset($_POST)) {

        $data = array();

        $json = json_decode($_POST['data'], true);

        $data['wkspace'] = "{$json[0]['wkspace']}";

        if (empty($data['wkspace'])) {
            $msg = 'ERROR: wkspace is empty in wkspaces|delete';
        } else {

            $class = new Wkspaces();

            if ($class->countWkspaceUsers($data) > 0) {
                $msg = "This workgroup contains users and cannot be deleted";
            } else if ($class->deleteWkspace($data)) {
                $status = 'success';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>