<?php

// modules.php

define("DOCUMENT_ROOT", "{$_SERVER['DOCUMENT_ROOT']}");

session_start();

if (empty($_SESSION)) {
    header("Location: ./index.php");
} else {

    define('MULTIDB_MODULE', true);
    define('USER_AUTHENTICATED', ($_SESSION['authenticated'] == 'yes') ? true : false);

    function __autoload($class_name) {
        require_once(DOCUMENT_ROOT . "/classes/class.$class_name.php");
    }

    date_default_timezone_set("{$_SESSION['time_zone']}");

    $input = array();

    $filters = array(
        'mod' => FILTER_SANITIZE_STRING
    );

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $input = filter_input_array(INPUT_GET, $filters);
            break;
        case 'POST':
            $input = filter_input_array(INPUT_POST, $filters);
            break;
    }

    if (empty($input['mod'])) {
        die("MODULES.PHP EXCEPTION: MOD IS EMPTY!");
    } else {

        $sub = '';
        $mod = "{$input['mod']}";
        $ext = '.php';

        $pos = strpos($mod, '|'); // find sub mods if any

        if ($pos !== false) {
            list($mod, $sub) = explode('|', $mod);
        }

        define("MODULAR_ROOT", DOCUMENT_ROOT . "/modules/$mod/");

        $file = (!empty($sub)) ? $sub . $ext : $mod . $ext;

        $ifile = MODULAR_ROOT . "$file";

        include_once($ifile);
    }
}
?>
