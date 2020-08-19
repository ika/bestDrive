<?php

// config.php

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
        w2ui['grid'].load('modules.php?mod=config|load');
    },
    unSelectLine: function () {
        w2ui['grid'].selectNone();
    },
    selectLine: function (recid) {
        w2ui['grid'].select(recid);
    },
    disableButtons: function () {
        w2ui['grid'].toolbar.disable('edit');
    },
    enableButtons: function () {
        w2ui['grid'].toolbar.enable('edit');
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
        header: '<b id="heading">CONFIGURATION</b>',
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
                {type: 'button', id: 'edit', caption: 'Edit', disabled: true}
            ],
            onClick: function (target, data) {

                switch (target) {

                    case 'edit':
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());

                            var form = w2ui.editForm;
                            form.record['recid'] = row.recid;
                            form.record['markup'] = row.markup;
                            form.record['version'] = row.version;
                            form.record['software'] = row.software;
                            form.record['domain'] = row.domain;
                            form.record['tzone'] = row.tzone;
                            form.record['upload'] = row.upload;
                            form.refresh();
                            openEditPopup();

                        break;
                }
            }
        },
        columns: [
            {field: 'recid', hidden: true},
            {field: 'markup', caption: 'markup', size: '10%'},
            {field: 'version', caption: 'version', size: '20%'},
            {field: 'software', caption: 'software', size: '20%'},
            {field: 'domain', caption: 'domain', size: '20%'},
            {field: 'tzone', caption: 'tzone', size: '20%'},
            {field: 'upload', caption: 'upload', size: '10%'}
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
            }
        }
    }

}

var FORMS = {
    editForm: {
        name: 'editForm',
        url: './modules.php',
        fields: [
            {name: 'markup', type: 'text', required: true,
                html: {caption: 'Markup %', attr: 'style="width: 20%;"'},
            },
            {name: 'version', type: 'text', required: true,
                html: {caption: 'Version', attr: 'style="width: 45%;"'},
            },
            {name: 'software', type: 'text', required: true,
                html: {caption: 'Software', attr: 'style="width: 45%;"'},
            },
            {name: 'domain', type: 'text', required: true,
                html: {caption: 'Domain', attr: 'style="width: 45%;"'},
            },
            {name: 'tzone', type: 'text', required: true,
                html: {caption: 'Time Zone', attr: 'style="width: 45%;"'},
            },
            {name: 'upload', type: 'text', required: true,
                html: {caption: 'Max Upload MB', attr: 'style="width: 20%;"'},
            }
        ],
        record: {
            recid: ''
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
            mod: 'config|edit'
        }
    }


}

function openEditPopup() {

    w2popup.open({
        title: '<b>catagory edit form</b>',
        width: 550,
        height: 350,
        showMax: false,
        body: '<div id="pop1" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop1').w2render('editForm');
            }
        },
        onClose: function (event) {
            JSC.unSelectLine();
        }
    });
}

$(function () {

    $('#toolbar').w2toolbar(TOOLS.toolbar);
    $('#grid').w2grid(GRIDS.grid);

    $('').w2form(FORMS.editForm);

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

