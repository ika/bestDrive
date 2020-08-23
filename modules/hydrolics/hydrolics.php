<?php

// hydrolics.php

if (!defined('MULTIDB_MODULE') || !USER_AUTHENTICATED) {
	die("UNAUTHORIZED ACCESS");
} else {

	$domain = "{$_SESSION['domain']}";
	$site = "<b>{$_SESSION['software']} {$_SESSION['version']}</b>";
	$name = "<b>{$_SESSION['full_name']}</b>";

	$maxfilesize = "{$_SESSION['maxUploadFileSize']}";

	$markup = (int) "{$_SESSION['markup']}";

	$hydro = new Hydrolics();
	$rows = $hydro->selectAllHydrolics();

	$tot = 0;
	$number = 0;

	$json = '[ ';
	foreach ($rows as $k => $v) {

		$nt = $v['cost'];
		$vnet = str_replace(",", "", $nt); // remove comma
		$vnet = 'R ' . number_format($vnet, 2, '.', ',');

		$number += $v['onhand'];
		$tot += $v['cost'];

		$s = ($hydro->duplicatePartNumber($v) > 1) ? "style:'background-color: #FBFEC0;'," : '';

		$json .= "{ $s recid: {$v['recid']}, partid: '{$v['partid']}', partname: '{$v['partname']}', size: '{$v['size']}', descr: '{$v['descr']}', date: '{$v['date']}', cost: '{$vnet}', onhand: '{$v['onhand']}' },";
	}
	//$tot = $tot / 100;
	$total = number_format($tot, 2, ".", ",");

	$json .= "{ summary: true, recid: 'S-1', date: '<span style=\"float: right;\"><b>Total</b></span>', cost:  'R $total', ";
	$json .= " summary: true, recid: 'S-2', rrp: '<span style=\"float: right;\"><b>Total</b></span>', onhand:  '$number' }";
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

	<div id="grid" style="width:100%;height:500px;margin:10px auto;overflow:hidden;"></div>

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
    encodeEntity : function(str) {
	var buf = [];
	for (var i=str.length-1;i>=0;i--) {
		buf.unshift(['&#', str[i].charCodeAt(), ';'].join(''));
	}
	return buf.join('');
    },
    decodeEntity: function(str) {
	return str.replace(/&#(\d+);/g, function(match, dec) {
		return String.fromCharCode(dec);
	});
    },
    getRandom: function (length) {
	return Math.floor(Math.pow(10, length - 1) + Math.random() * 9 * Math.pow(10, length - 1));
    },
    time: function () {
	return (new Date().getTime());
    },
    reload: function () {
	//w2ui['grid'].load('modules.php?mod=tyres|load');
	window.location = './modules.php?mod=hydrolics';
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
	header: '<b id="heading">HYDROLICS</b>',
	show: {
	    toolbar: true,
	    header: true,
	    footer: true,
	    lineNumbers: true
	},
	multiSearch: true,
	searches: [
	    {field: 'partid', caption: 'ID', type: 'text'},
	    {field: 'partname', caption: 'Part Number', type: 'text'},
	    {field: 'size', caption: 'Part Size', type: 'text'},
	    {field: 'desdr', caption: 'Description', type: 'text'}
	],
	toolbar: {
	    items: [
		{type: 'break'},
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

		    case 'price_adjust':
			var row = w2ui['grid'].get(w2ui['grid'].getSelection());
			var form = w2ui.priceAdjustForm;
			form.clear();
			form.record['recid'] = row.recid;
			form.record['partid'] = row.partid;
			form.record['cost'] = row.cost;
			form.refresh();
			openPriceAdjustPopup();
			break;

		    case 'stock_adjust':
			var row = w2ui['grid'].get(w2ui['grid'].getSelection());
			var form = w2ui.stockAdjustForm;
			form.clear();
			form.record['recid'] = row.recid;
			form.record['partid'] = row.partid;
			form.record['onhand'] = row.onhand;
			form.refresh();
			openStockAdjustPopup();
			break;

		    case 'add':
			JSC.unSelectLine();
			var form = w2ui.addForm;
			form.record['partid'] = 'HY-' + JSC.getRandom(7);
			form.record['date'] = w2utils.formatDate((new Date()), 'dd/mm/yyyy');
			openAddPopup();
			break;

		    case 'edit':
			var row = w2ui['grid'].get(w2ui['grid'].getSelection());
			var form = w2ui.editForm;
			form.record['recid'] = row.recid;
			form.record['partid'] = row.partid;
			form.record['partname'] = JSC.decodeEntity(row.partname);
			form.record['size'] = JSC.decodeEntity(row.size);
			form.record['date'] = row.date;
			form.record['descr'] = JSC.decodeEntity(row.descr);
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
	    {field: 'partname', caption: 'Part Name', size: '10%', sortable: true}, 
            {field: 'size', caption: 'Part Size', size: '10%', sortable: true},
	    {field: 'descr', caption: 'Description', size: '10%', sortable: true},
	    {field: 'date', caption: 'Date', size: '3%', sortable: true},
	    {field: 'cost', caption: 'Cost', size: '3%', sortable: false},
	    {field: 'onhand', caption: 'O/H', size: '3%', sortable: true}
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
	    {name: 'partname', type: 'text', required: false,
		html: {caption: 'Part Name', attr: 'style="width: 60%;"'},
	    },
	    {name: 'size', type: 'text', required: false,
		html: {caption: 'Part Size', attr: 'style="width: 60%;"'},
	    },
	    {name: 'descr', type: 'textarea', required: false,
		html: {caption: 'Description', attr: 'style="width: 80%;height: 100px;"'},
	    },
	    {name: 'date', type: 'date', required: true,
		html: {caption: 'Date', attr: 'style="width: 30%;"'},
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
	    mod: 'hydrolics|add'
	}
    },
    editForm: {
	name: 'editForm',
	url: './modules.php',
	fields: [
	    {name: 'partid', type: 'text', required: false,
		html: {caption: 'ID', attr: 'style="width: 30%;" disabled'},
	    },
	    {name: 'partname', type: 'text', required: false,
		html: {caption: 'Part Name', attr: 'style="width: 60%;"'},
	    },
	    {name: 'size', type: 'text', required: false,
		html: {caption: 'Part Size', attr: 'style="width: 60%;"'},
	    },
	    {name: 'descr', type: 'textarea', required: false,
		html: {caption: 'Description', attr: 'style="width: 80%;height: 100px;"'},
	    },
	    {name: 'date', type: 'date', required: true,
		html: {caption: 'Date', attr: 'style="width: 30%;"'},
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
	    mod: 'hydrolics|edit'
	}
    },
    priceAdjustForm: {
	name: 'priceAdjustForm',
	url: './modules.php',
	fields: [
	    {name: 'partid', type: 'text', required: false,
		html: {caption: 'ID', attr: 'style="width: 40%;" disabled'}
	    },
	    {name: 'cost', type: 'money', required: true,
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
	    mod: 'hydrolics|price'
	}
    },
    stockAdjustForm: {
	name: 'stockAdjustForm',
	url: './modules.php',
	fields: [
	    {name: 'partid', type: 'text', required: false,
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
	    mod: 'hydrolics|onhand'
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
	title: '<b>Add Hydrolics Form</b>',
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
    w2utils.settings.date_format = 'dd/mm/yyyy'

    $('#toolbar').w2toolbar(TOOLS.toolbar);
    $('#grid').w2grid(GRIDS.grid);

    $('').w2form(FORMS.addForm);
    $('').w2form(FORMS.editForm);
    $('').w2form(FORMS.priceAdjustForm);
    $('').w2form(FORMS.stockAdjustForm);
//    $('').w2form(FORMS.importCvsForm);

});

</script>

</body>
</html>
EndOfPage;

	exit($page);
}

?>
