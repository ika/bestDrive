<?php

// tyres|buy

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'error';

    if (isset($_POST)) {

        $data = array();

        $data['recid'] = "{$_POST['record']['recid']}";
        $data['ramount'] = trim($_POST['record']['amount']);

        $data['amount'] = number_format($data['ramount'], 2, '.', '');
        $data['retail'] = number_format($data['ramount'] + (($data['ramount'] / 100) * 80), 2, '.', '');

        $class = new Tyres();

        if ($class->updatePrices($data)) {
            $status = 'success';
        } else {
            error_log('ERROR: prices not updated in tyres|padjust');
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
