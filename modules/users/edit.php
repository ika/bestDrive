<?php

// users|edit

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = "Edit user error";

    if (isset($_POST)) {

        $data = array();

        $data['recid'] = "{$_POST['record']['recid']}";
        $data['firstname'] = trim($_POST['record']['firstname']);
        $data['lastname'] = trim($_POST['record']['lastname']);
        $data['email'] = trim($_POST['record']['email']);
        $data['telephone'] = trim($_POST['record']['telephone']);
        $data['notes'] = trim($_POST['record']['notes']);
        $data['active'] = ($_POST['record']['active'] == 1) ? 'yes' : 'no';
        $data['password'] = trim($_POST['record']['password']);
        $data['wkspaces'] = "{$_POST['record']['wkspaces']['id']}";
        $data['start'] = "{$_POST['record']['start']['id']}";
        
        if($data['active'] == 'no') {
            $data['start'] = 'none';
        }

        if (empty($data['recid'])) {
            error_log('ERROR: recid missing in users|edit');
        } else {
            $users = new Users();

            if ($users->checkOtherEmailExists($data)) {
                $msg = "This email address is already in use<br />({$data['email']})";
            } else {

                if ($users->editUser($data)) {
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
