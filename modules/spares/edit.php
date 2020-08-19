<?php

// spares|edit

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = "error";

    if (isset($_POST)) {

        $data = array();

        $data['recid'] = "{$_POST['record']['recid']}";
        $data['partid'] = trim($_POST['record']['partid']);
        $data['partnumber'] = "{$_POST['record']['partnumber']}";
        $data['suppinvoice'] = "{$_POST['record']['suppinvoice']}";
        $data['datein'] = trim($_POST['record']['datein']);
        $data['jobcard'] = trim($_POST['record']['jobcard']);
        $data['description'] = trim($_POST['record']['description']);
        $data['cost'] = trim($_POST['record']['cost']);
        $data['onhand'] = trim($_POST['record']['onhand']);

        if (empty($data['recid'])) {
            error_log('ERROR: recid missing in spares|edit');
        } else {

            if (!empty($data['cost'])) {
                $data['cost'] = number_format($data['cost'], 2, '.', '');
            }

            $class = new Spares();

            if ($class->editSpares($data)) {
                $status = 'success';
            } else {
                $msg = 'ERROR: spares not updated';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
