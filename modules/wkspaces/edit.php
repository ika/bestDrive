<?php

// edit.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'undefined error';

    if (isset($_POST)) {

        $data = array();

        $data['title'] = trim($_POST['record']['title']);
        $data['wkspace'] = "{$_POST['record']['wkspace']}";

        if (empty($data['title'])) {
            $msg = " You must enter a workspace title";
        } else {

            $class = new Wkspaces();

            if ($class->editWkspace($data)) {
                $status = 'success';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
