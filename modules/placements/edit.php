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
        $data['name'] = trim($_POST['record']['name']);
        $data['descr'] = "{$_POST['record']['descr']}";

        if (empty($data['name'])) {
            $msg = " You must enter a name";
        } else {

            $class = new Placements();

            if ($class->editPlace($data)) {
                $status = 'success';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
