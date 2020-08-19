<?php

// index.php

define("DOCUMENT_ROOT", "{$_SERVER['DOCUMENT_ROOT']}");

session_start();

$_SESSION = array();

function __autoload($class_name) {
    require_once(DOCUMENT_ROOT . "/classes/class.$class_name.php");
}

$class = new Config();
$class->selectConfig();

$_SESSION['authenticated'] = 'no';

header("Location: ./modules.php?mod=login");

exit();
?>