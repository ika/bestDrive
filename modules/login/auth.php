<?php

// auth.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $status = 'error';
    $msg = 'Undefined login error';
    $start = 'none';

    if (isset($_POST)) {

        $data = array();

        $data['email'] = "{$_POST['record']['email']}";
        $data['password'] = "{$_POST['record']['password']}";
        $data['check'] = ($_POST['record']['check'] == 1) ? 'yes' : 'no';

        $login = new Login();
        $login->Authenticate($data);

        $code = new Code();

        if (empty($login->data['email'])) {
            $msg = _("Your email is not known");
        } else {

            if ($login->data['active'] == 'no') {
                $msg = "Your account is not active<br />Contact the administrator";
            } else {

                if ($login->data['active'] == 'del') {
                    $msg = "Your account has been deleted<br />Contact the administrator";
                } else {

                    if (empty($login->data['start'])) {
                        $msg = "Your account start page has not been set.<br />Contact the administrator";
                    } else {

                        if (base64_encode($data['password']) !== $login->data['password']) {
                            $msg = "Your password did not match your email";
                        } else {

                            $status = 'success';

                            $_SESSION['user_email'] = "{$login->data['email']}";
                            $_SESSION['full_name'] = "{$login->data['firstname']} {$login->data['lastname']}";
                            $_SESSION['firstname'] = "{$login->data['firstname']}";
                            $_SESSION['lastname'] = "{$login->data['lastname']}";
                            $_SESSION['wkspace'] = "{$login->data['wkspace']}";
                            $_SESSION['uid'] = "{$login->data['userid']}";
                            $_SESSION['authenticated'] = 'yes';

                            $start = "{$login->data['start']}";


//                    // reports
//                    $_SESSION['startDate'] = strtotime( 'first day of ' . date( 'F Y')); // first day of the month
//                    $d = date('d-m-Y', time());
//                    $d = $d . ' 23:59:59';
//                    $_SESSION['endDate'] = strtotime($d); // one sec before midnight

                            $login->lastLogin();

                            if ($data['check'] == 'yes') {
                                $expires = 60 * 60 * 24 * 30 + time(); // 30 days
                                $cookie_data = "{$data['email']}|{$data['password']}";
                                $cookie_data = $code->enCode($cookie_data);
                            } else {
                                $expires = time() - 3600;
                                $cookie_data = '';
                            }

                            setcookie('MultiDBLogin', "$cookie_data", $expires, '/');
                        }
                    }
                }
            }
        }
    }

    $response = json_encode(array("status" => "$status", "message" => "$msg", "start" => "$start"));

    exit($response);
}
?>
