<?php

// users|add

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'error';

    if (isset($_POST)) {

        $data = array();

        $data['firstname'] = trim($_POST['record']['firstname']);
        $data['lastname'] = trim($_POST['record']['lastname']);
        $data['telephone'] = trim($_POST['record']['telephone']);
        $data['email'] = trim($_POST['record']['email']);
        $data['notes'] = trim($_POST['record']['notes']);
        $data['active'] = ($_POST['record']['active'] == 1) ? 'yes' : 'no';
        $data['password'] = trim($_POST['record']['password']);
        $data['wkspaces'] = "{$_POST['record']['wkspaces']['id']}";
        $data['start'] = "{$_POST['record']['start']['id']}";

        $users = new Users();

        if ($users->checkEmailExists($data)) {
            $msg = "This email address is already in use<br />({$data['email']})";
        } else {
            if ($users->addUser($data)) {
                $status = 'success';
            } else {
                error_log( 'ERROR: user not added');
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
