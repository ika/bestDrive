<?php

// users|load.php

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $users = new Users();

    $rows = $users->selectUsers();

    $c = count($rows);

    $data = '{ "total":' . $c . ',"records":' . json_encode($rows) . '}';

    exit($data);
}
?>