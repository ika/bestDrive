<?php

// users|load.php

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $class = new Tyres();

    $rows = $class->selectTyres();

    $c = count($rows);

    $rows = json_encode($rows);

    $data = <<<EOT
{
"total": $c,
"records": $rows
}
EOT;

    exit($data);
}
?>