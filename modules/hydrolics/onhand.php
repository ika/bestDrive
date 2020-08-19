<?php

// hydrolics|onhand.php

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'error';

    if (isset($_POST)) {

        $data = array();

        $data['recid'] = "{$_POST['record']['recid']}";
        $data['onhand'] = "{$_POST['record']['onhand']}";

        $class = new Hydrolics();

        if ($class->updateOnhand($data)) {
            $status = 'success';
        } else {
            error_log('ERROR: onhand not updated in tyres|sadjust');
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
