<?php

// tyres|add

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'error';

    if (isset($_POST)) {

        $data = array();

        $data['id'] = strtoupper(trim($_POST['record']['id']));
        $data['seg'] = "{$_POST['record']['seg']['id']}";
        $data['place'] = "{$_POST['record']['place']['id']}";
        $data['brand'] = strtoupper(trim($_POST['record']['brand']));
        $data['rim'] = trim($_POST['record']['rim']);
        $data['size'] = trim($_POST['record']['size']);
        $data['design'] = trim($_POST['record']['design']);
        $data['ssr'] = strtoupper(trim($_POST['record']['ssr']));
        $data['li'] = trim($_POST['record']['li']);
        $data['si'] = strtoupper(trim($_POST['record']['si']));
        $data['cost'] = trim($_POST['record']['cost']);
        $data['onhand'] = trim($_POST['record']['onhand']);

        if (!empty($data['cost'])) {
            $data['cost'] = number_format($data['cost'], 2, '.', '');
            //$data['retail'] = number_format($data['rcost'] + (($data['rcost'] / 100) * 80), 2, '.', '');
        } else {
            $data['cost'] = '00.00';
            //$data['retail'] = '00.00';
        }

        $class = new Tyres();

        if ($class->addTyre($data)) {
            $status = 'success';
        } else {
            $msg = 'ERROR: tyre not added';
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
