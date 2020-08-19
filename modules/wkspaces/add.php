<?php

// add.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'undefined error';

    if (isset($_POST)) {

        $data = array();

        $data['title'] = trim($_POST['record']['title']);

        if (empty($data['title'])) {
            $msg = " You must enter a workspace title";
        } else {

            $class = new Wkspaces();

            $data['wkuid'] = uniqid();

            if ($class->addWkspace($data)) {
                $status = 'success';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
