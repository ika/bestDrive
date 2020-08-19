<?php

// split type size and rim

define("DOCUMENT_ROOT", "{$_SERVER['DOCUMENT_ROOT']}");

require_once(DOCUMENT_ROOT . "/classes/class.DBConnection.php");
require_once(DOCUMENT_ROOT . "/classes/class.Session.php");

$session = new Session();
session_start();

function __autoload($class_name) {
    require_once(DOCUMENT_ROOT . "/classes/class.$class_name.php");
}

$class = new Tyres();

$rows = $class->selectTyres();

foreach ($rows as $k => $v) {

    $data = array();

    $pos = stripos($v['size'], 'R');

    if ($pos !== false) {
        $s = substr($v['size'], 0, $pos);
        $r = substr($v['size'], $pos);

        $data['recid'] = "{$v['recid']}";
        $data['size'] = $s;
        $data['rim'] = $r;

        if ($class->tyreSizeRimSplit($data)) {
            echo "{$v['recid']} - {$v['size']} - $s - $r<br />";
        }
    }
}
?>

