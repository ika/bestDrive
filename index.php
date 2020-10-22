<?php

// index.php
//
define("DOCUMENT_ROOT", "{$_SERVER['DOCUMENT_ROOT']}");

session_start();

$config = require_once(DOCUMENT_ROOT . '/../config.php');

$_SESSION = array();
$_SESSION['authenticated'] = $config->authenticated;
$_SESSION['version'] = $config->version;
$_SESSION['software'] = $config->software;
$_SESSION['markup'] = $config->markup;
$_SESSION['domain'] = $config->domain;
$_SESSION['time_zone'] = $config->timeZone;

$mb = (int) $config->uploadMb;
$_SESSION['maxUploadFileSize'] = (1024 * 1024 * $mb); // 32MB

header("Location: ./modules.php?mod=login");

exit();
?>
