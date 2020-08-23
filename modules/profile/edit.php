<?php

// profile|edit

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = "Edit profile error";

    if (isset($_POST)) {

        $data = array();

        $data['recid'] = "{$_POST['record']['recid']}";
        $data['firstname'] = trim($_POST['record']['firstname']);
        $data['lastname'] = trim($_POST['record']['lastname']);
        $data['email'] = trim($_POST['record']['email']);
        $data['telephone'] = trim($_POST['record']['telephone']);
        $data['notes'] = trim($_POST['record']['notes']);
        $data['password'] = trim($_POST['record']['password']);
        
        if (empty($data['recid'])) {
            error_log('ERROR: recid missing in profile|edit');
        } else {
            $class = new Profile();

            if ($class->checkOtherEmailExists($data)) {
                $msg = "This email address is already in use<br />({$data['email']})";
            } else {

                if ($class->updateProfile($data)) {
                    $status = 'success';
                } else {
                    error_log('ERROR: user not edited');
                }
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
