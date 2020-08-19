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

        $data['recid'] = "{$json[0]['recid']}";

        if (empty($data['recid'])) {
            $msg = 'ERROR: recid is empty in placements|delete';
        } else {

            $class = new Placements();

            if ($class->removePlace($data)) {
                $status = 'success';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>