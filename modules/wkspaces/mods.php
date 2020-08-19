<?php

// add.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'undefined message';

    if (isset($_POST)) {

        $data = array();

        $json = json_decode($_POST['data'], true);

        $data['wkspace'] = "{$json[0]['wkspace']}";
        $data['modid'] = trim($json[0]['modid']);

        $switch = "{$json[0]['func']}";

        if (empty($data['wkspace'])) {
            $msg = 'ERROR: wkspace error in wkspaces|mods';
        } else if (empty($data['modid'])) {
            $msg = 'ERROR: modid error in wkspaces|mods';
        } else {

            $class = new Modules();

            switch ($switch) {

                case 'add':
                    if ($class->addMod($data)) {
                        $status = 'success';
                    }
                    break;
                case 'remove':
                    if ($class->removeMod($data)) {
                        $status = 'success';
                    }
                    break;
                default:
                    $msg = 'ERROR: switch error in wkspaces|mods';
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg"));

    exit($response);
}
?>
