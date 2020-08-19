<?php

// login.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $domain = "{$_SESSION['domain']}";
    $site = "<b>{$_SESSION['software']} {$_SESSION['version']}</b>";

    $code = new Code();

    $email = $pass = '';
    $cookie_data = (isset($_COOKIE['MultiDBLogin'])) ? "{$_COOKIE['MultiDBLogin']}" : '';
    if (!empty($cookie_data)) {
        $cookie_data = $code->deCode($cookie_data);
        list($email, $pass) = explode('|', $cookie_data);
    }

    $page = <<<EndOfPage
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>$domain</title>
            
    <link rel="stylesheet" type="text/css" href="lib/icons/css/all.css" />
    <link rel="stylesheet" type="text/css" href="lib/w2ui/w2ui-1.4.3.min.css" />

    <link rel="stylesheet" type="text/css" href="lib/base.css" />

    <script type="text/javascript" charset="utf8" src="lib/jquery/jquery-2.2.4.min.js"></script>
    <script type="text/javascript" charset="utf8" src="lib/w2ui/w2ui-1.4.3.min.js"></script>
            
            

</head>
<body>
    <div>
        <div id="head">
            <span><img id="headerlogo" src="lib/images/tiretracker.png" alt="Logo" ></span>
        </div>

        <div id="toolbarHolder" ></div>

        <div id="formHolder" style="width:640px; margin:30px auto;" ></div>

    </div>

<script type="text/javascript">
            
var MENUS = {
    about: function () {
        w2popup.load({url: './modules.php?mod=about'});
    }
}

var JSC = {
    time: function () {
        return (new Date().getTime());
    }

}

var TOOLS = {
    toolBar: {
        name: 'toolBar',
        style: 'background-color: #F0F0C1',
        items: [
            {type: 'button', id: 'site', caption: '$site'}
        ],
        onClick: function(target, data) {
            switch(target) {
                case 'site':
                    MENUS.about();
                break;
            }
        } 
    }
}

var FORMS = {
    loginForm: {
        name   : 'loginForm',
        header : 'Login Form',
        url    : './modules.php',
        fields: [
            {name: 'email', type: 'text', required: true,
                html: {caption: 'email', attr: 'size="40"'}
            },
            {name: 'password', type: 'password', required: true,
                html: {caption: 'password', attr: 'size="40"'}
            },
            {name: 'check', type: 'checkbox',
                html: {caption: '&nbsp;', text: ' remember me on this computer'}
            }
        ],
        record: {
            "email": '$email',
            "password": '$pass',
            "check": 1
        },
        actions: {
            login: function () {
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        window.location = './modules.php?mod=' + resp.start;
                    }
                });
            }
        },
        postData: {
            mod: 'login|auth'
        }
    }

}

$(function () {

    $('#toolbarHolder').w2toolbar(TOOLS.toolBar);
    $('#formHolder').w2form(FORMS.loginForm);

});

</script>
</body>
</html>
EndOfPage;

    exit($page);
}
?>

