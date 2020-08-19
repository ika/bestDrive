<?php

// users|mods

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'Access Denied<br />Contact your System Administrator';

    if (isset($_POST)) {

        $json = json_decode($_POST['data'], true);

        $mod = "{$json[0]['mod']}";

        if (empty($mod)) {
            $msg = 'ERROR: mod missing in users|mods';
        } else {
            $mods = array();
            $class = new Modules();
            $mods['modules'] = $class->returnModules();

            if (in_array($mod, $mods['modules'])) {
                $status = 'success';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
