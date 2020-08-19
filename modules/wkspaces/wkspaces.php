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
        w2ui['grid'].load('modules.php?mod=wkspaces|load');
    },
    unSelectLine: function () {
        w2ui['grid'].selectNone();
    },
    selectLine: function (recid) {
        w2ui['grid'].select(recid);
    },
    disableButtons: function () {
        w2ui['grid'].toolbar.disable('mods');
        w2ui['grid'].toolbar.disable('edit');
        w2ui['grid'].toolbar.disable('delete');
    },
    enableButtons: function () {
        w2ui['grid'].toolbar.enable('mods');
        w2ui['grid'].toolbar.enable('edit');
        w2ui['grid'].toolbar.enable('delete');
    },
    wksDelete: function (data) {

        var ts = JSC.time();

        $.post('./modules.php', {
            mod: 'wkspaces|delete',
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
    modAdd: function(data) {

        var ts = JSC.time();

        $.post('./modules.php', {
            mod  : 'wkspaces|mods',
            data : data,
            ts     : ts
        }, function(resp) {
            if(resp.status != 'success') {
                console.log(resp.message);
            }
        }, 'json');
    },
    modRemove: function(data) {

        var ts = JSC.time();

        $.post('./modules.php', {
            mod  : 'wkspaces|mods',
            data : data,
            ts     : ts
        }, function(resp) {
            if(resp.status != 'success') {
                console.log(resp.message);
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
        header: '<b id="heading">WORKGROUPS</b>',
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
                {type: 'button', id: 'config', caption: 'Config', disabled: false},
                {type: 'spacer'},
                {type: 'button', id: 'add', caption: 'Add', disabled: false},
                {type: 'break'},
                {type: 'button', id: 'mods', caption: 'Modules', disabled: true},
                {type: 'break'},
                {type: 'button', id: 'edit', caption: 'Edit', disabled: true},
                {type: 'break'},
                {type: 'button', id: 'delete', caption: 'Delete', disabled: true}
            ],
            onClick: function (target, data) {

                switch (target) {
            
                    case 'config':
                        JSC.unSelectLine();
                        openConfigGrid();
                        break;

                    case 'add':
                        JSC.unSelectLine();
                        openAddPopup();
                        break;
            
                     case 'mods':
            
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
                        var form = w2ui.modsForm;
            
                        var jsonModsArray = [];
                        if((row.modules !== undefined) && (row.modules !== null) && (row.modules !== "")) {
                            var parts = row.modules.split(",");
                            for (var i = 0; i < parts.length; i++) {
                                  var p = jQuery.trim(parts[i]);
                                  jsonModsArray.push ( { "id" : p } );
                            }
                        }
            
                        form.record['modules'] = jsonModsArray;
                        form.record['wkspace'] = row.wkspace;
                        form.refresh();
                        openModsPopup();
                        break;

                    case 'edit':
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
                        var form = w2ui.editForm;
            
                        form.record['wkspace'] = row.wkspace;
                        form.record['title'] = row.title;
                        form.refresh();
                        openEditPopup();
                        break;

                    case 'delete':

                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());

                        w2confirm('Are you sure you want to delete this workspace?' + '<br />' + '(' + row.title + ')', function btn(answer) {

                            if (answer == 'Yes') {

                                var objArr = [];
                                var obj = {};
                                obj.wkspace = row.wkspace;
                                objArr.push(obj);
                                JSC.wksDelete(JSON.stringify(objArr));
                            } else {
                                JSC.unSelectLine();
                            }
                        });

                        break;
                }
            }
        },
        columns: [
            {field: 'title', caption: 'title', size: '40%' },
            {field: 'modules', caption: 'modules', size: '60%'}
        ],
        record: {
            "recid": '',
            "wkspace": ''
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
    modsForm: {
        name: 'modsForm',
        url: './modules.php',
        formHTML:
                '<div id="form" style="width: 550px;" >' +
                '<div class="w2ui-page page-0">' +
                '      <div class="w2ui-field">' +
                '          <label>title:</label>' +
                '                <div><input name="modules" type="text" maxlength="100" size="40" /></div>' +
                '       </div>' +
                '</div>',
        fields: [
            {name: 'modules', type: 'enum', required: true,
                options: {
                    openOnFocus: true,
                    items: [ 'wkspaces', 'users', 'tyres', 'segments', 'placements','config', 'spares', 'hydrolics'],
                    onRemove: function (event) {

                            var ObjArr = [];
                            var Obj = {};
                            Obj.wkspace = w2ui['modsForm'].record['wkspace'];
                            Obj.modid = event.item.id;
                            Obj.func = 'remove';
                            ObjArr.push(Obj);
            
                             //console.log('modsForm Removed: ' + JSON.stringify(ObjArr));

                            JSC.modRemove(JSON.stringify(ObjArr));

                    },
                    onAdd: function (event) {

                            var ObjArr = [];
                            var Obj = {};
                            Obj.wkspace = w2ui['modsForm'].record['wkspace'];
                            Obj.modid = event.item.id;
                            Obj.func = 'add';
                            ObjArr.push(Obj);
            
                             //console.log('modsForm Added: ' + JSON.stringify(ObjArr));

                            JSC.modAdd(JSON.stringify(ObjArr));

                    }
                }
            }
        ],
        record: {
            //modules: [{id: 'workspaces', text: 'workspaces'}, {id: 'users', text: 'users'}, {id: 'tyres', text: 'tyres'}],
            "modules": '',
            "wkspace": '',
        }
    },
    addForm: {
        name: 'addForm',
        url: './modules.php',
        formHTML:
                '<div id="form" style="width: 550px;" >' +
                '<div class="w2ui-page page-0">' +
                '      <div class="w2ui-field">' +
                '          <label>title:</label>' +
                '                <div><input name="title" type="text" maxlength="100" size="40" /></div>' +
                '       </div>' +
                ' </div>' +
                '<div class="w2ui-buttons">' +
                '   <button class="btn" name="save">Save</button>' +
                '</div>' +
                '</div>',
        fields: [
            {name: 'title', type: 'text', required: true }
        ],
        record: {
            title: ''
        },
        actions: {
            save: function () {
               // console.log(w2ui.addForm.record);
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        },
        postData: {
            mod: 'wkspaces|add'
        }
    },
    editForm: {
        name: 'editForm',
        url: './modules.php',
        formHTML:
                '<div id="form" style="width: 550px;" >' +
                '<div class="w2ui-page page-0">' +
                '      <div class="w2ui-field">' +
                '          <label>title:</label>' +
                '                <div><input name="title" type="text" maxlength="100" size="40" /></div>' +
                '       </div>' +
                ' </div>' +
                '<div class="w2ui-buttons">' +
                '   <button class="btn" name="save">Save</button>' +
                '</div>' +
                '</div>',
        fields: [
            {name: 'title', type: 'text', required: true}
        ],
        record: {
            title: '',
            wkspace: ''
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
            mod: 'wkspaces|edit'
        }
    }


}

function openEditPopup() {

    w2popup.open({
        title: '<b>workspace edit form</b>',
        width: 650,
        height: 200,
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
            
function openAddPopup() {

    w2popup.open({
        title: '<b>workspace add form</b>',
        width: 650,
        height: 200,
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

function openModsPopup() {

    w2popup.open({
        title: '<b>modules form</b>',
        width: 650,
        height: 200,
        showMax: false,
        body: '<div id="pop2" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop2').w2render('modsForm');
            }
        },
        onClose: function (event) {
            JSC.reload();
        }
    });
}
            
function openConfigGrid() {
    JSC.mods('config');
}

$(function () {

    $('#toolbar').w2toolbar(TOOLS.toolbar);
    $('#grid').w2grid(GRIDS.grid);

    $('').w2form(FORMS.addForm);
    $('').w2form(FORMS.editForm);
    $('').w2form(FORMS.modsForm);

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

