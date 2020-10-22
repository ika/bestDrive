<?php

// profile.php

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $class = new Profile();

    $row = $class->getProfile();

    ob_start();
    include('profile.js');
    $content = ob_get_clean();

    $content = str_replace('[>recid<]', "{$row['recid']}", $content);
    $content = str_replace('[>firstname<]', "{$row['firstname']}", $content);
    $content = str_replace('[>lastname<]', "{$row['lastname']}", $content);
    $content = str_replace('[>email<]', "{$row['email']}", $content);
    $content = str_replace('[>password<]', "{$row['password']}", $content);
    $content = str_replace('[>telephone<]', "{$row['telephone']}", $content);
    $content = str_replace('[>notes<]', "{$row['notes']}", $content);

    exit($content);
}
?>

