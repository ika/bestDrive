<?php

// lastuser.php
// get the name of the user who last modified an entry

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'undefined error';
    $rows = array();
    $name = "";

    if (isset($_POST)) {

        $data = array();

        $json = json_decode($_POST['data'], true);

        $data['recid'] = "{$json[0]['recid']}";

        if (empty($data['recid'])) {
            error_log('lastuser EXCEPTION: no recid');
        } else {

            $class = new Tyres();

            if ($rows = $class->getLastModifiedUser($data)) {
                $name = $rows[0]['name'];
                $status = 'success';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg", "data" => "$name"));

    exit($response);
}
?>

