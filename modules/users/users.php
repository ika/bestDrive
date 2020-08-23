<?php

// users|users

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

//    function placement() {
//        $cookie = "{$_SESSION['user_email']}";
//        $expires = 60 * 60 * 24 * 30 + time(); // 30 days
//        setcookie($cookie, 'users', $expires, '/');
//    }
//
//    placement();

    $domain = "{$_SESSION['domain']}";
    $site = "<b>{$_SESSION['software']} {$_SESSION['version']}</b>";
    $name = "<b>{$_SESSION['full_name']}</b>";
    //$email = "<b>{$_SESSION['user_name']}</b>";
    //$version = "<b>{$_SESSION['version']}</b>";

//    $uspass = genMd5Password(6);

    $class = new Wkspaces();
    $wksps = $class->makeWksList();
    unset($class);

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
            <span id="headericons" >
                <i class="fas fa-tint" onclick="MENUS.hydrolics();" ></i>
                <i class="fas fa-wrench" onclick="MENUS.spares();" ></i>
                <i class="fas fa-car" onclick="MENUS.tyres();" ></i>
                <i class="fas fa-user" onclick="MENUS.users();" ></i>
                <i class="fas fa-cogs" onclick="MENUS.wkspaces();" ></i>
                <i class="fas fa-sign-out-alt" onclick="MENUS.exit();" ></i>
            </span>
        </div>

        <div id="toolbar" ></div>

        <div id="grid" style="width:100%; margin:10px auto; height:500px; overflow: hidden;" ></div>

    </div>

<script type="text/javascript">

var MENUS = {
    hydrolics: function () {
        JSC.mods('hydrolics');
    },
    spares: function () {
        JSC.mods('spares');
    },
    reports: function () {
        JSC.mods('reports');
    },
    tyres: function () {
        JSC.mods('tyres');
    },
    users: function () {
        JSC.mods('users');
    },
    wkspaces: function () {
        JSC.mods('wkspaces');
    },
    about: function () {
        w2popup.load({url: './modules.php?mod=about'});
    },
   // profile: function () {
   //     w2popup.load({url: './modules.php?mod=profile'});
   // },
    exit: function () {
        w2confirm('Are you sure you want to exit?', function btn(answer) {
            if (answer == 'Yes') {
                window.location = './index.php';
            }
        });
    }
}

var JSC = {
    time: function () {
        return (new Date().getTime());
    },
    reload: function () {
        w2ui['grid'].load('modules.php?mod=users|load');
    },
    unSelectLine: function () {
        w2ui['grid'].selectNone();
    },
    selectLine: function (recid) {
        w2ui['grid'].select(recid);
    },
    disableButtons: function () {
        w2ui['grid'].toolbar.disable('edit');
        w2ui['grid'].toolbar.disable('delete');
        w2ui['grid'].toolbar.disable('password');
    },
    enableButtons: function () {
        w2ui['grid'].toolbar.enable('edit');
        w2ui['grid'].toolbar.enable('delete');
        w2ui['grid'].toolbar.enable('password');
    },
    suggestAdPass: function (l) {
        var pw = Math.random().toString(36).slice(-l);
        var form = w2ui.addForm;
        form.record['password']  = pw;
        form.refresh();
    },
    suggestEdPass: function (l) {
        var pw = Math.random().toString(36).slice(-l);
        var form = w2ui.editForm;
        form.record['password']  = pw;
        form.refresh();
    },
    deleteUser: function (data) {

        var ts = JSC.time();

        $.post('./modules.php', {
            mod: 'users|delete',
            data: data,
            ts: ts
        }, function (resp) {
            if (resp.status == 'success') {
                JSC.reload();
            } else {
                w2alert(resp.message);
            }
        }, 'json');
    },
    mods: function (mod) {
            
        var objArr = [];
        var obj = {};
        obj.mod = mod;
        objArr.push(obj);

        var ts = JSC.time();

        $.post('./modules.php', {
            mod: 'perms|mods',
            data: JSON.stringify(objArr),
            ts: ts
        }, function (resp) {
            if (resp.status == 'success') {
                window.location = './modules.php?mod=' + mod;
            } else {
                w2alert(resp.message);
            }
        }, 'json');
    }

}
            
var startItems = ['wkspaces', 'users', 'tyres', 'spares', 'hydrolics', 'none'];

var GRIDS = {
    grid: {
        name: 'grid',
        autoload: true,
        header: '<b id="heading">USERS</b>',
        method: 'GET',
        show: {
            toolbar: true,
            header: true,
            footer: true,
            lineNumbers: true,
            toolbarSearch: true,
        },
        toolbar: {
            items: [
                {type: 'spacer'},
                {type: 'button', id: 'add', caption: 'Add', disabled: false},
                {type: 'break'},
                {type: 'button', id: 'edit', caption: 'Edit', disabled: true},
                {type: 'break'},
                {type: 'button', id: 'delete', caption: 'Delete', disabled: true}
               // {type: 'button', id: 'password', caption: 'password', icon: 'fa fa-unlock', disabled: true}
            ],
            onClick: function (target, data) {

                switch (target) {

                    case 'add':
                        JSC.unSelectLine();
                        var form = w2ui.addForm;
                        form.clear();
                        form.record['password'] = Math.random().toString(36).slice(-6);
                        form.record['active'] = '1';
                        form.refresh();
                        openAddPopup();
                        break;

                    case 'edit':
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
                        var form = w2ui.editForm;
                        form.record['recid'] = row.recid;
                        form.record['firstname'] = row.firstname;
                        form.record['lastname'] = row.lastname;
                        form.record['email'] = row.email;
                        form.record['password'] = row.password;
                        form.record['telephone'] = row.telephone;
                        form.record['notes'] = row.notes;
                        form.record['active'] = (row.active == 'yes') ? 1 : 0;
                        form.record['wkspaces'] = row.wksid;
                        form.record['start'] = row.start;
                        form.refresh();
                        openEditPopup();
                        break;

                    case 'delete':

                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
                        w2confirm('Are you sure you want to delete this record?' + '<br />' + '(' + row.fullname + ')', function btn(answer) {

                            if (answer == 'Yes') {

                                var objArr = [];
                                var obj = {};
                                obj.recid = row.recid;
                                obj.userid = row.userid;
                                objArr.push(obj);
                                JSC.deleteUser(JSON.stringify(objArr));
                            } else {
                                JSC.unSelectLine();
                            }
                        });

                        break;

                    case 'password':
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
                        var form = w2ui.passForm;
                        form.record['recid'] = row.recid;
                        form.record['clear'] = '';
                        form.record['repeat'] = '';
                        form.refresh();
                        openPassPopup();
                        break;

                }
            }
        },
        columns: [
            {field: 'fullname', caption: 'name', size: '20%', sortable: true},
            {field: 'email', caption: 'email', size: '25%', sortable: true},
            {field: 'telephone', caption: 'telephone', size: '10%', sortable: true},
            {field: 'active', caption: '<div style="text-align:center">active</div>', size: '5%', sortable: true, attr: 'align=center'},
            {field: 'start', caption: 'start page', size: '10%', sortable: false,
                render: function (record, index, col_index) {
                    var item = this.getCellValue(index, col_index);
                    //return startItems.includes(item) ? item : '';
                    // better browser support
                    return (startItems.indexOf(item) !=-1) ? item : ''; 
                }   
             },
            {field: 'wkspace', caption: 'workgroup', size: '15%', sortable: true},
            {field: 'lastlogin', caption: 'last login', size: '25%'},
        ],
        record: {
            "recid": '',
            "firstname": '',
            "lastname": '',
            "notes": '',
            "start": '',
            "wksid":'',
            "userid": ''
        },
        multiSearch: true,
        searches: [
            {field: 'fullname', caption: 'name', type: 'text'},
            {field: 'email', caption: 'email', type: 'text'}
        ],
        onSelect: function (event) {
            JSC.enableButtons();
        },
        onUnselect: function (event) {
            JSC.disableButtons();
        }

    }

}

var TOOLS = {
    toolbar: {
        name: 'toolbar',
        style: 'background-color: #F0F0C1',
        items: [
            {type: 'button', id: 'site', caption: '$site'},
            {type: 'spacer'},
            {type: 'button', id: 'name', caption: '$name'}
        ],
        onClick: function (target, data) {
            switch (target) {
                case 'site':
                    MENUS.about();
                    break;
                case 'name':
                    //MENUS.profile();
                    break;
            }
        }
    }

}

var FORMS = {
    addForm: {
        name: 'addForm',
        url: './modules.php',
        formHTML:
                '<div id="form" style="width: 750px;">' +
                '<div class="w2ui-page page-0">' +
                '    <div style="width: 550px; float: left; margin-right: 0px;">' +
                '        <div class="w2ui-group" style="height: 320px;"> ' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>name:</label>' +
                '                    <div><input name="firstname" type="text" maxlength="100" style="width: 100%" ></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>surname:</label>' +
                '                <div><input name="lastname" type="text" maxlength="100" style="width: 100%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>email:</label>' +
                '                <div><input name="email" type="text" maxlength="100" style="width: 100%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>password:</label>' +
                '                <div><input name="password" type="text" maxlength="100" style="width: 80%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>telephone:</label>' +
                '                <div><input name="telephone" type="text" maxlength="100" style="width: 80%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>workspace:</label>' +
                '                <div><input name="wkspaces" type="text" maxlength="100" style="width: 80%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>start:</label>' +
                '                <div><input name="start" type="text" maxlength="100" style="width: 50%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>notes:</label>' +
                '                <div><textarea name="notes" type="text" style="width: 100%; height: 60px; resize: none;" /></div>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '    <div style="width: 200px; float: right; margin-left: 0px;">' +
                '        <div class="w2ui-group" style="height: 320px;">' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>active:</label>' +
                '                <div><input name="active" type="checkbox" /></div>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '</div>' +
                '<div class="w2ui-buttons">' +
                '   <button class="btn" style="margin-top:15px;" onclick="JSC.suggestAdPass(6)" >suggest a new password</button>' +
                '   <button class="btn" name="save">Save</button>' +
                '</div>' +
                '</div>',
        fields: [
            {name: 'firstname', type: 'text', required: true},
            {name: 'lastname', type: 'text', required: true},
            {name: 'email', type: 'email', required: true},
            {name: 'telephone', type: 'text', required: false},
            {name: 'password', type: 'text', required: true},
            {name: 'wkspaces', type: 'list', required: true,
                options: {
                    items: $wksps
                }
            },
            {name: 'start', type: 'list', required: true,
                options: {
                    items: startItems
                }
            },
            {name: 'notes', type: 'textarea', required: false},
            {name: 'active', type: 'checkbox'}
        ],
        record: {
            firstname: '',
            lastname: '',
            email: '',
            telephone: '',
            password: '',
            notes: '',
            active: '1'
        },
        actions: {
            save: function () {
                //console.log(w2ui.addForm.record);
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        },
        postData: {
            mod: 'users|add'
        }
    },
    editForm: {
        name: 'editForm',
        url: './modules.php',
        formHTML:
                '<div id="form" style="width: 750px;">' +
                '<div class="w2ui-page page-0">' +
                '    <div style="width: 550px; float: left; margin-right: 0px;">' +
                '        <div class="w2ui-group" style="height: 320px;"> ' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>name:</label>' +
                '                    <div><input name="firstname" type="text" maxlength="100" style="width: 100%" ></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>surname:</label>' +
                '                <div><input name="lastname" type="text" maxlength="100" style="width: 100%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>email:</label>' +
                '                <div><input name="email" type="text" maxlength="100" style="width: 100%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>password:</label>' +
                '                <div><input name="password" type="text" maxlength="100" style="width: 80%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>telephone:</label>' +
                '                <div><input name="telephone" type="text" maxlength="100" style="width: 80%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>workspace:</label>' +
                '                <div><input name="wkspaces" type="text" maxlength="100" style="width: 80%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>start:</label>' +
                '                <div><input name="start" type="text" maxlength="100" style="width: 50%" /></div>' +
                '            </div>' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>notes:</label>' +
                '                <div><textarea name="notes" type="text" style="width: 100%; height: 60px; resize: none;" /></div>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '    <div style="width: 200px; float: right; margin-left: 0px;">' +
                '        <div class="w2ui-group" style="height: 320px;">' +
                '            <div class="w2ui-field w2ui-span4">' +
                '                <label>active:</label>' +
                '                <div><input name="active" type="checkbox" /></div>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '</div>' +
                '<div class="w2ui-buttons">' +
                '   <button class="btn" style="margin-top:15px;" onclick="JSC.suggestEdPass(6)" >suggest a new password</button>' +
                '   <button class="btn" name="save">Save</button>' +
                '</div>' +
                '</div>',
        fields: [
            {name: 'firstname', type: 'text', required: true},
            {name: 'lastname', type: 'text', required: true},
            {name: 'email', type: 'email', required: true},
            {name: 'telephone', type: 'text', required: false},
            {name: 'password', type: 'text', required: true},
            {name: 'wkspaces', type: 'list', required: true,
                options: {
                    items: $wksps
                }
            },
            {name: 'start', type: 'list', required: true,
                options: {
                    items: startItems
                }
            },
            {name: 'notes', type: 'textarea', required: false},
            {name: 'active', type: 'checkbox'}
        ],
        record: {
            firstname: '',
            lastname: '',
            email: '',
            telephone: '',
            password: '',
            notes: '',
            active: '',
            start: ''
        },
        actions: {
            save: function () {
                //console.log(w2ui.editForm.record);
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        },
        postData: {
            mod: 'users|edit'
        }
    }
//    passForm: {
//        name: 'passForm',
//        url: './modules.php',
//        fields: [
//            {name: 'clear', type: 'text', required: true,
//                html: {caption: 'password', attr: 'size="20"'}
//            },
//            {name: 'repeat', type: 'text', required: true,
//                html: {caption: 'repeat password',
//                    attr: 'size="20"', text: '<div><button class="btn" style="margin-top:15px;" onclick="JSC.suggestPass(6)" >suggest a new password</button></div>'}
//            }
//        ],
//        record: {
//            recid: ''
//        },
//        actions: {
//            save: function () {
//                this.save(function (resp) {
//                    if (resp.status == 'success') {
//                        w2popup.close();
//                    }
//                });
//            }
//        },
//        postData: {
//            mod: 'users|passwd'
//        }
//    }


}

function openAddPopup() {
    w2popup.open({
        title: '<b>user add form</b>',
        width: 810,
        height: 480,
        showMax: false,
        body: '<div id="pop1" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop1').w2render('addForm');
            }
        },
        onClose: function (event) {
            JSC.unSelectLine();
        }
    });
}

function openEditPopup() {
    w2popup.open({
        title: '<b>user edit form</b>',
        width: 810,
        height: 480,
        showMax: false,
        body: '<div id="pop2" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop2').w2render('editForm');
            }
        },
        onClose: function (event) {
            JSC.unSelectLine();
        }
    });
}

//function openPassPopup() {
//    w2popup.open({
//        title: '<b>password change form</b>',
//        width: 640,
//        height: 260,
//        showMax: false,
//        body: '<div id="pop3" style="width: 100%; height: 100%;"></div>',
//        style: 'padding: 10px 10px 10px 10px',
//        onOpen: function (event) {
//            event.onComplete = function () {
//                $('#w2ui-popup #pop3').w2render('passForm');
//            }
//        },
//        onClose: function (event) {
//            JSC.unSelectLine();
//        }
//    });
//}

$(function () {

    $('#toolbar').w2toolbar(TOOLS.toolbar);
    $('#grid').w2grid(GRIDS.grid);

    $('').w2form(FORMS.addForm);
    $('').w2form(FORMS.editForm);
    //$('').w2form(FORMS.passForm);

    JSC.reload();

});

</script>

</body>
</html>
EndOfPage;

    print($page);
}

exit();
?>

