<?php

// add.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'undefined error';

    if (isset($_POST)) {

        $data = array();

        $data['name'] = trim($_POST['record']['name']);
        $data['descr'] = trim($_POST['record']['descr']);

        if (empty($data['name'])) {
            $msg = " You must enter a catagory name";
        } else {

            $class = new Segments();

            if ($class->addSeg($data)) {
                $status = 'success';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
