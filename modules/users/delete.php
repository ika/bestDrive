<?php

// users|delete.php

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'undefined status';
    $msg = 'undefined error';

    if (isset($_POST)) {

        $data = array();

        $json = json_decode($_POST['data'], true);

        $data['recid'] = "{$json[0]['recid']}";
        $data['userid'] = "{$json[0]['userid']}";

        if (empty($data['recid'])) {
            $msg = 'ERROR: recid is empty in users|delete';
        } else {

            $users = new Users();

//            $status = $users->delUserCheck($data);
//
//            if ($status['time'] == 'NONE') {
//                if ($users->deleteUser($data)) {
//                    $status = 'success';
//                }
//            } else {
//                $msg = 'You cannot delete this user<br/>(this information is needed for record purposes)<br/>de-activate to prevent login';
//            }

            if ($users->deleteUser($data)) {
                $status = 'success';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>