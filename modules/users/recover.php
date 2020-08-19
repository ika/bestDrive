<?php

// users.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'undefined status';

    if (isset($_GET)) {

        $data = array();

        $data['recid'] = trim($_GET['recid']);
        $data['pass'] = trim($_GET['pass']);

        if (empty($data['recid'])) {
            $status = "you must enter the database us_id as recid<br/>EXAMPLE: http://domain_name/modules.php?mod=users|recover&recid=25&pass=xxxxxx";
        } else if (empty($data['pass'])) {
            $status = "you must enter the password as pass<br/>EXAMPLE: http://domain_name/modules.php?mod=users|recover&recid=25&pass=xxxxxx";
        } else {

            $data['password'] = base64_encode($data['pass']);

            $class = new Users();

            if ($class->recoverUserPassword($data)) {
                $status = "Passoword has been changed to {$data['pass']} for user with ID {$data['recid']}";
            } else {
                $status = "password recovery did not succeed";
            }
        }
    }

    exit($status);
}
?>
