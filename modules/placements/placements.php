<?php

// wkspaces.php

if (!defined('MULTIDB_MODULE')) {
    die("UNAUTHORIZED ACCESS");
} else {

    $domain = "{$_SESSION['domain']}";
    $site = "<b>{$_SESSION['software']} {$_SESSION['version']}</b>";
    $name = "<b>{$_SESSION['full_name']}</b>";

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

        <div id="toolbar"></div>

        <div id="grid" style="width:95%; margin:30px auto; height:500px; overflow: hidden;" ></div>

    </div>

<script type="text/javascript">
            
var modsList = [];

var MENUS = {
    hydrolics: function () {
        JSC.mods('hydrolics');
    },
    spares: function () {
        JSC.mods('spares');
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
        w2ui['grid'].load('modules.php?mod=placements|load');
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
    },
    enableButtons: function () {
        w2ui['grid'].toolbar.enable('edit');
        w2ui['grid'].toolbar.enable('delete');
    },
    delete: function (data) {

        var ts = JSC.time();

        $.post('./modules.php', {
            mod: 'placements|delete',
            data: data,
            ts: ts
        }, function (resp) {
            if (resp.status == 'success') {
                JSC.reload();
            } else {
               JSC.unSelectLine();
               JSC.disableButtons();
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

var GRIDS = {
    grid: {
        name: 'grid',
        autoload: true,
        header: '<b id="heading">TYRE PLACEMENTS</b>',
        method: 'GET',
        show: {
            toolbar: true,
            header: true,
            footer: true,
            lineNumbers: true,
            toolbarSearch: false,
            multiSearch: false
        },
        toolbar: {
            items: [
                {type: 'spacer'},
                {type: 'button', id: 'add', caption: 'Add', disabled: false},
                {type: 'break'},
                {type: 'button', id: 'edit', caption: 'Edit', disabled: true},
                {type: 'break'},
                {type: 'button', id: 'delete', caption: 'Delete', disabled: true}
            ],
            onClick: function (target, data) {

                switch (target) {

                    case 'add':
                        JSC.unSelectLine();
                        var form = w2ui.addForm;
            
                        form.record['name'] = '';
                        form.record['descr'] = '';
                        form.refresh();
                        openAddPopup();
                        break;

                    case 'edit':
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
            
                        if(row.name == 'Gen') {
                            var form = w2ui.editFormDisabled;
                            form.record['recid'] = row.recid;
                            form.record['name'] = row.name;
                            form.record['descr'] = row.descr;
                            form.refresh();
                            openEditDisabledPopup();
                        } else {
                            var form = w2ui.editForm;
                            form.record['recid'] = row.recid;
                            form.record['name'] = row.name;
                            form.record['descr'] = row.descr;
                            form.refresh();
                            openEditPopup();
                        }
                        break;

                    case 'delete':

                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
            
                        if(row.name == 'Gen') {
                            w2alert('This entry cannot be deleted');
                        } else {

                            w2confirm('Are you sure you want to delete this catagory?' + '<br />' + '(' + row.name + ')', function btn(answer) {

                                if (answer == 'Yes') {

                                    var objArr = [];
                                    var obj = {};
                                    obj.recid = row.recid;
                                    objArr.push(obj);
                                    JSC.delete(JSON.stringify(objArr));
                                } else {
                                    JSC.unSelectLine();
                                }
                            });
            
                        }

                        break;
                }
            }
        },
        columns: [
            {field: 'recid', hidden: true},
            {field: 'name', caption: 'name', size: '40%'},
            {field: 'descr', caption: 'description', size: '60%'}
        ],
        record: {
            "recid": '',
        },
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
            }
        }
    }

}

var FORMS = {
    addForm: {
        name: 'addForm',
        url: './modules.php',
        fields: [
            {name: 'name', type: 'text', required: true, 
                html: {caption: 'Name', attr: 'style="width: 30%;"'},
            },
            {name: 'descr', type: 'text', required: true,
                html: {caption: 'Description', attr: 'style="width: 45%;"'},
            }
        ],
        record: {
            name: '',
            descr: ''
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
            mod: 'placements|add'
        }
    },
    editForm: {
        name: 'editForm',
        url: './modules.php',
        fields: [
            {name: 'name', type: 'text', required: true, 
                html: {caption: 'Name', attr: 'style="width: 30%;"'},
            },
            {name: 'descr', type: 'text', required: true,
                html: {caption: 'Description', attr: 'style="width: 45%;"'},
            }
        ],
        record: {
            recid: ''
        },
        actions: {
            save: function () {
                console.log(w2ui.editForm.record);
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        },
        postData: {
            mod: 'placements|edit'
        }
    },
    editFormDisabled: {
        name: 'editFormDisabled',
        url: './modules.php',
        fields: [
            {name: 'name', type: 'text', required: true, 
                html: {caption: 'Name', attr: 'style="width: 30%;" disabled'},
            },
            {name: 'descr', type: 'text', required: true,
                html: {caption: 'Description', attr: 'style="width: 45%;"'},
            }
        ],
        record: {
            recid: ''
        },
        actions: {
            save: function () {
                console.log(w2ui.editForm.record);
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        },
        postData: {
            mod: 'placements|edit'
        }
    }


}

function openEditPopup() {

    w2popup.open({
        title: '<b>catagory edit form</b>',
        width: 550,
        height: 220,
        showMax: false,
        body: '<div id="pop1" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop1').w2render('editForm');
            }
        }
    });
}
            
function openEditDisabledPopup() {

    w2popup.open({
        title: '<b>catagory edit form</b>',
        width: 550,
        height: 220,
        showMax: false,
        body: '<div id="pop1" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop1').w2render('editFormDisabled');
            }
        }
    });
}
            
function openAddPopup() {

    w2popup.open({
        title: '<b>catagory add form</b>',
        width: 550,
        height: 220,
        showMax: false,
        body: '<div id="pop1" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop1').w2render('addForm');
            }
        }
    });
}

$(function () {

    $('#toolbar').w2toolbar(TOOLS.toolbar);
    $('#grid').w2grid(GRIDS.grid);

    $('').w2form(FORMS.addForm);
    $('').w2form(FORMS.editForm);
    $('').w2form(FORMS.editFormDisabled);

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

