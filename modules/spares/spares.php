<?php

// spares.php

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
    die("UNAUTHORIZED ACCESS");
} else {

    $domain = "{$_SESSION['domain']}";
    $site = "<b>{$_SESSION['software']} {$_SESSION['version']}</b>";
    $name = "<b>{$_SESSION['full_name']}</b>";

    $maxfilesize = "{$_SESSION['maxUploadFileSize']}";

    $markup = (int) "{$_SESSION['markup']}";

    $spares = new Spares();
    $rows = $spares->selectAllSpares();

    $plac = new Placements();
    $places = $plac->placeSelector();

    $tot = 0;
    $number = 0;

    $json = '[ ';
    foreach ($rows as $k => $v) {

        $nt = $v['cost'];
        $vnet = str_replace(",", "", $nt); // remove comma
        $vnet = 'R ' . number_format($vnet, 2, '.', ',');
//        
//        
//        $a = (int) $nt;
//
//        $mk = $a + (($a / 100) * $markup);
//        $vrrp = 'R ' . number_format($mk, 2, '.', ',');
//        
//        $o = (int) $v['onhand'];
//        $t = ($a * $o);
//
//        $tot += $t;
//        
//        $number += $o;

        $s = ($spares->duplicatePartNumber($v) > 1) ? "style:'background-color: #FBFEC0;'," : '';

        $json .= "{ $s recid: {$v['recid']}, partid: '{$v['partid']}', partnumber: '{$v['partnumber']}', description: '{$v['description']}', suppinvoice: '{$v['suppinvoice']}', datein: '{$v['datein']}', jobcard: '{$v['jobcard']}', cost: '{$vnet}', onhand: '{$v['onhand']}' },";
    }
    //$tot = $tot / 100;
    //$total = number_format($tot, 2, ".", ",");
    //$json .= "{ summary: true, recid: 'S-1', ssr: '<span style=\"float: right;\"><b>Total</b></span>', net:  'R$total', ";
    //$json .= " summary: true, recid: 'S-2', rrp: '<span style=\"float: right;\"><b>Total</b></span>', onhand:  '$number' }";
    $json .= ' ]';

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
    print: function (cat) {
            
            switch(cat) {
            
                case 'all':
                window.location = './modules.php?mod=spares|print';
                break;
            
            }
        
    },
    export: function () {
        w2confirm('Export a TAB separated file?', function btn(answer) {
            if (answer == 'Yes') {
                window.location = './modules.php?mod=tyres|export';
            }
        });
    },
    profile: function () {
        w2popup.load({url: './modules.php?mod=profile'});
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
    getRandom: function (length) {
        return Math.floor(Math.pow(10, length - 1) + Math.random() * 9 * Math.pow(10, length - 1));
    },
    time: function () {
        return (new Date().getTime());
    },
    reload: function () {
        //w2ui['grid'].load('modules.php?mod=tyres|load');
        window.location = './modules.php?mod=spares';
    },
    unSelectLine: function () {
        w2ui['grid'].selectNone();
    },
    selectLine: function (recid) {
        w2ui['grid'].select(recid);
    },
    disableButtons: function () {
        w2ui['grid'].toolbar.disable('last_user');
        w2ui['grid'].toolbar.disable('price_adjust');
        w2ui['grid'].toolbar.disable('stock_adjust');
        w2ui['grid'].toolbar.disable('edit');
    },
    enableButtons: function () {
        w2ui['grid'].toolbar.enable('last_user');
        w2ui['grid'].toolbar.enable('price_adjust');
        w2ui['grid'].toolbar.enable('stock_adjust');
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
    },
    lastUser: function (data) {

        var ts = JSC.time();

        $.post('./modules.php', {
            mod: 'spares|lastuser',
            data: data,
            ts: ts
        }, function (resp) {
            if (resp.status == 'success') {
                w2alert(resp.data);
            } else {;
                w2alert(resp.message);
            }
        }, 'json');

    }


}

var GRIDS = {
    grid: {
        name: 'grid',
        autoload: true,
        header: '<b id="heading">SPARES</b>',
        show: {
            toolbar: true,
            header: true,
            footer: true,
            lineNumbers: true
        },
        multiSearch: true,
        searches: [
            {field: 'partid', caption: 'ID', type: 'text'},
            {field: 'description', caption: 'Description', type: 'text'},
            {field: 'partnumber', caption: 'Part Number', type: 'text'},
            {field: 'suppinvoice', caption: 'Suppliers Inv Number', type: 'text'},
            {field: 'datein', caption: 'Suppliers Inv Date', type: 'text'},
            {field: 'jobcard', caption: 'Job Card No', type: 'text'}
        ],
        toolbar: {
            items: [
                {type: 'break'},
                {type: 'menu', id: 'ditem', caption: 'Print',
                    options: {},
                    items: [
                        {id: 'printall', text: 'Spares'}
//                        {id: 'printadditions', text: 'extra'},
//                        {id: 'printractor', text: 'tractor'},
//                        {id: 'printmach', text: 'machine'},
//                        {id: 'printbmw', text: 'BMW'}
                    ]
                },
                {type: 'break'},
//                {type: 'menu', id: 'vitem', caption: 'Import',
//                    items: [
//                        {id: 'bestdrive', text: 'BestDrive'}
//                    ]
//                },
//                {type: 'break'},
//                {type: 'button', id: 'export', caption: 'Export', disabled: false},
//                {type: 'break'},
                {type: 'button', id: 'segments', caption: 'Segments', disabled: false},
                {type: 'break'},
//                {type: 'button', id: 'placements', caption: 'Placements', disabled: false},
//                {type: 'break'},
                {type: 'button', id: 'last_user', caption: 'Last User', disabled: true},
                {type: 'spacer'},
                {type: 'button', id: 'add', caption: 'Add', disabled: false},
                {type: 'break'},
                {type: 'button', id: 'price_adjust', caption: 'Cost', disabled: true},
                {type: 'break'},
                {type: 'button', id: 'stock_adjust', caption: 'O/H', disabled: true},
                {type: 'break'},
                {type: 'button', id: 'edit', caption: 'Edit', disabled: true}
            ],
            onClick: function (target, data) {

                switch (target) {
            
                    case 'last_user':
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
                        //JSC.lastUser(row.recid);
                        var objArr = [];
                        var obj = {};
                        obj.recid = row.recid;
                        objArr.push(obj);
                        JSC.lastUser(JSON.stringify(objArr));
                        break;
            
                    case 'segments':
                        JSC.unSelectLine();
                        //openCatagoriesGrid();
                        alert('under construction');
                        break;

                    case 'export':
                        MENUS.export();
                        break;

                    case 'vitem:bestdrive':
                        JSC.unSelectLine();
                        var form = w2ui.importCvsForm;
                        form.record['name'] = '';
                        form.refresh();
                        openImportPopup();
                        break;

                    case 'ditem:printall':
                        MENUS.print('all');
                        break;
            
                    case 'ditem:printadditions':
                        MENUS.print('additions');
                        break;
            
                    case 'ditem:printractor':
                        MENUS.print('tractor');
                        break;
            
                    case 'ditem:printmach':
                        MENUS.print('machine');
                        break;
            
                    case 'ditem:printbmw':
                        MENUS.print('bmw');
                        break;

                    case 'price_adjust':
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
                        var form = w2ui.priceAdjustForm;
                        form.clear();
                        form.record['recid'] = row.recid;
                        form.record['tid'] = row.partid;
                        form.record['amount'] = row.cost;
                        form.refresh();
                        openPriceAdjustPopup();
                        break;

                    case 'stock_adjust':
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
                        var form = w2ui.stockAdjustForm;
                        form.clear();
                        form.record['recid'] = row.recid;
                        form.record['tid'] = row.partid;
                        form.record['onhand'] = row.onhand;
                        form.refresh();
                        openStockAdjustPopup();
                        break;

                    case 'add':
                        JSC.unSelectLine();
                        var form = w2ui.addForm;
                        form.record['partid'] = 'SP-' + JSC.getRandom(7);
                        form.refresh();
                        openAddPopup();
                        break;

                    case 'edit':
                        var row = w2ui['grid'].get(w2ui['grid'].getSelection());
                        var form = w2ui.editForm;
                        form.record['recid'] = row.recid;
                        form.record['partid'] = row.partid;
                        form.record['partnumber'] = row.partnumber;
                        form.record['suppinvoice'] = row.suppinvoice;
                        form.record['datein'] = row.datein;
                        form.record['jobcard'] = row.jobcard;
                        form.record['description'] = row.description;
                        form.record['cost'] = row.cost;
                        form.record['onhand'] = row.onhand;
                        form.refresh();
                        openEditPopup();
                        break;
                }
            }
        },
        columns: [
            {field: 'recid', hidden: true},
            {field: 'partid', caption: 'ID', size: '3%', sortable: true },
            {field: 'description', caption: 'Description', size: '10%', sortable: true},
            {field: 'partnumber', caption: 'Part Number', size: '3%', sortable: true},
            {field: 'suppinvoice', caption: 'Supp Inv No', size: '3%', sortable: true},
            {field: 'datein', caption: 'Supp Inv Date', size: '3%', sortable: true},
            {field: 'jobcard', caption: 'Job Card No', size: '3%', sortable: true},
            {field: 'cost', caption: 'Cost', size: '3%', sortable: false},
            {field: 'onhand', caption: 'O/H', size: '1%', sortable: true}
        ],
        records: $json,
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
                    //openAboutPopup();
                    break;
                case 'name':
		     MENUS.profile();
                    break;
            }
        }
    }

}
            
            
//                options: {
//                    items: [{id: 'Pass', text: 'Pass'}, 
//                        {id: 'Van', text: 'Van'}, 
//                        {id: '4x4', text: '4x4'}, 
//                        {id: 'Trac', text: 'Trac'},
//                        {id: 'Mach', text: 'Mach'}]
//                    },

var FORMS = {
    addForm: {
        name: 'addForm',
        url: './modules.php',
        fields: [
            {name: 'partid', type: 'text', required: false,
                html: {caption: 'ID', attr: 'style="width: 30%;" disabled'},
            },
            {name: 'place', type: 'list', required: true,
                options: {
                    items: $places
                    },
                html: {caption: 'Placement', attr: 'style="width: 45%;"'}
            },
            {name: 'partnumber', type: 'text', required: false,
                html: {caption: 'Part Number', attr: 'style="width: 60%;"'},
            },
            {name: 'suppinvoice', type: 'text', required: false,
                html: {caption: 'Supp Inv No', attr: 'style="width: 60%;"'},
            },
            {name: 'datein', type: 'text', required: false,
                html: {caption: 'Supp Inv Date', attr: 'style="width: 30%;"'},
            },
            {name: 'jobcard', type: 'text', required: false,
                html: {caption: 'Job Card No', attr: 'style="width: 60%;"'},
            },
            {name: 'description', type: 'text', required: true,
                html: {caption: 'Description', attr: 'style="width: 60%;"'},
            },
            {name: 'cost', type: 'money', required: true,
                html: {caption: 'Cost', attr: 'style="width: 25%;"'},
            },
            {name: 'onhand', type: 'text', required: true,
                html: {caption: 'On Hand', attr: 'style="width: 25%;"'},
            }
        ],
        record: {
            cost: '0.00',
            onhand: '0'
        },
        actions: {
            save: function () {
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        },
        postData: {
            mod: 'spares|add'
        }
    },
    editForm: {
        name: 'editForm',
        url: './modules.php',
        fields: [
            {name: 'partid', type: 'text', required: false,
                html: {caption: 'ID', attr: 'style="width: 30%;" disabled' },
            },
            {name: 'partnumber', type: 'text', required: false,
                html: {caption: 'Part Number', attr: 'style="width: 60%;"'},
            },
            {name: 'suppinvoice', type: 'text', required: false,
                html: {caption: 'Supp Inv No', attr: 'style="width: 60%;"'},
            },
            {name: 'datein', type: 'text', required: false,
                html: {caption: 'Supp Inv Date', attr: 'style="width: 30%;"'},
            },
            {name: 'jobcard', type: 'text', required: false,
                html: {caption: 'Job Card No', attr: 'style="width: 60%;"'},
            },
            {name: 'description', type: 'text', required: true,
                html: {caption: 'Description', attr: 'style="width: 60%;"'},
            },
            {name: 'cost', type: 'money', required: true,
                html: {caption: 'Cost', attr: 'style="width: 25%;"'},
            },
            {name: 'onhand', type: 'text', required: true,
                html: {caption: 'On Hand', attr: 'style="width: 25%;"'},
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
            mod: 'spares|edit'
        }
    },
    priceAdjustForm: {
        name: 'priceAdjustForm',
        url: './modules.php',
        fields: [
            {name: 'tid', type: 'text', required: false,
                html: {caption: 'ID', attr: 'style="width: 40%;" disabled'}
            },
            {name: 'amount', type: 'money', required: true,
                html: {caption: 'Unit Price (EX VAT)', attr: 'style="width: 40%;"'},
            }
        ],
        record: {
            recid: ''
        },
        actions: {
            save: function () {
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        },
        postData: {
            mod: 'spares|padjust'
        }
    },
    stockAdjustForm: {
        name: 'stockAdjustForm',
        url: './modules.php',
        fields: [
            {name: 'tid', type: 'text', required: false,
                html: {caption: 'ID', attr: 'style="width: 40%;" disabled'}
            },
            {name: 'onhand', type: 'text', required: true,
                html: {caption: 'On Hand', attr: 'style="width: 40%;"'},
            }
        ],
        record: {
            recid: ''
        },
        actions: {
            save: function () {
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        },
        postData: {
            mod: 'spares|sadjust'
        }
    },
    importCvsForm: {
        name: 'importCvsForm',
        fields: [
            {name: 'name', id: 'name', type: 'file', required: true,
                options: {max: 1, maxFileSize: 0, silent: false},
                html: {caption: 'File', attr: 'style="width: 400px;"'}
            }
        ],
        actions: {

            save: function () {

                var name = this.record['name']

                if (name == null || name == "") {
                    $("#name").w2tag('Required');
                } else {

                    var fileInfo = $('#name').data('selected');

                    var ObjArr = [];
                    var Obj = {};
                    Obj.content = fileInfo[0]['content'];
                    ObjArr.push(Obj);
                    JSC.fileSave(JSON.stringify(ObjArr));
                }
            }
        }
    }


}

function openAddPopup() {
    w2popup.open({
        title: '<b>Add Spares Form</b>',
        width: 560,
        height: 450,
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

function openEditPopup() {
    w2popup.open({
        title: '<b>Edit Spares Form</b>',
        width: 560,
        height: 450,
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

function openImportPopup() {
    w2popup.open({
        title: '<b>Import CSV Form</b>',
        width: 620,
        height: 200,
        showMax: false,
        body: '<div id="pop3" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop3').w2render('importCvsForm');
            }
        }
    });
}

function openPriceAdjustPopup() {
    w2popup.open({
        title: '<b>Price Adjustment Form</b>',
        width: 500,
        height: 200,
        showMax: false,
        body: '<div id="pop4" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop4').w2render('priceAdjustForm');
            }
        },
        onClose: function (event) {
            JSC.unSelectLine();
        },
        onKeydown : function (event) {
            if(event.originalEvent.keyCode === 13) {
                w2ui.priceAdjustForm.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        }
    });
}

function openStockAdjustPopup() {
    w2popup.open({
        title: '<b>Stock Adjustment Form</b>',
        width: 500,
        height: 200,
        showMax: false,
        body: '<div id="pop5" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop5').w2render('stockAdjustForm');
            }
        },
        onClose: function (event) {
            JSC.unSelectLine();
        },
        onKeydown : function (event) {
            if(event.originalEvent.keyCode === 13) {
                w2ui.stockAdjustForm.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                        JSC.reload();
                    }
                });
            }
        }
    });
}
            
function openCatagoriesGrid() {
    JSC.mods('segments');
}
            
function openPlacementsGrid() {
    JSC.mods('placements');
}

$(function () {

    w2utils.settings.currencyPrefix = 'R';
    w2utils.settings.currencyPrecision = '2';

    $('#toolbar').w2toolbar(TOOLS.toolbar);
    $('#grid').w2grid(GRIDS.grid);

    $('').w2form(FORMS.addForm);
    $('').w2form(FORMS.editForm);
    $('').w2form(FORMS.priceAdjustForm);
    $('').w2form(FORMS.stockAdjustForm);
    $('').w2form(FORMS.importCvsForm);

});

</script>

</body>
</html>
EndOfPage;

    print($page);
}

exit();
?>
