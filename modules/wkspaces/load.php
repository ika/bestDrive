<?php

// load.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $class = new Wkspaces();

    $rows = $class->selectWkspaces();

    $c = count($rows);

    $data = '{ "total":' . $c . ',"records":' . json_encode($rows) . '}';

    exit($data);
}
?>