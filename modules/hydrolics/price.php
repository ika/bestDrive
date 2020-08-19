<?php

// hydrolics|price

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'error';

    if (isset($_POST)) {

        $data = array();

        $data['recid'] = "{$_POST['record']['recid']}";
        $data['cost'] = trim($_POST['record']['cost']);

        //$data['cost'] = number_format($data['cost'], 2, '.', '');
        //$data['retail'] = number_format($data['cost'] + (($data['cost'] / 100) * 80), 2, '.', '');

        $class = new Hydrolics();

        if ($class->updatePrices($data)) {
            $status = 'success';
        } else {
            error_log('ERROR: prices not updated in hydrolics|price');
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
