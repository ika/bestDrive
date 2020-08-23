<script type="text/javascript">

var JSCP = {
	suggestProfilePass: function (len) {
        	var pw = Math.random().toString(36).slice(-len);
        	var form = w2ui.profileEditForm;
        	form.record['password']  = pw; 
		form.refresh();
	}
}

var FORMS = {
    profileEditForm: {
        name: 'profileEditForm',
        url: './modules.php',
        formHTML:
                '<div id="form" style="width: 760px;">' +
                '<div class="w2ui-page page-0">' +
                '    <div style="width: 760px; float: left; margin-right: 0px;">' +
                '        <div class="w2ui-group" style="height: 315px;"> ' +
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
                '                <label>notes:</label>' +
                '                <div><textarea name="notes" type="text" style="width: 100%; height: 80px; resize: none;" /></div>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '</div>' +
                '<div class="w2ui-buttons">' +
                '   <button class="btn" style="margin-top:15px;" onclick="JSCP.suggestProfilePass(6)" >suggest a new password</button>' +
                '   <button class="btn" name="save">Save</button>' +
                '</div>' +
                '</div>',
        fields: [
            {name: 'firstname', type: 'text', required: true},
            {name: 'lastname', type: 'text', required: true},
            {name: 'email', type: 'email', required: true},
            {name: 'telephone', type: 'text', required: false},
            {name: 'password', type: 'text', required: true},
            {name: 'notes', type: 'textarea', required: false}
        ],
        record: {
            firstname: '',
            lastname: '',
            email: '',
            telephone: '',
            password: '',
            notes: ''
        },
        actions: {
            save: function () {
                //console.log(w2ui.profileEditForm.record);
                this.save(function (resp) {
                    if (resp.status == 'success') {
                        w2popup.close();
                    }
                });
            }
        },
        postData: {
            mod: 'profile|edit'
        }
    }


}

function openProfilePopup() {
    w2popup.open({
        title: '<b>user edit form</b>',
        width: 810,
        height: 480,
        showMax: false,
        body: '<div id="pop5" style="width: 100%; height: 100%;"></div>',
        style: 'padding: 10px 10px 10px 10px',
        onOpen: function (event) {
            event.onComplete = function () {
                $('#w2ui-popup #pop5').w2render('profileEditForm');
            }
        },
        onClose: function (event) {}
    });
}

$(function () {

	$('').w2form(FORMS.profileEditForm);

	var form = w2ui.profileEditForm;
	form.record['recid'] = "[>recid<]";
	form.record['firstname'] = "[>firstname<]";
	form.record['lastname'] = "[>lastname<]";
	form.record['email'] = "[>email<]";
	form.record['password'] = "[>password<]";
	form.record['telephone'] = "[>telephone<]";
	form.record['notes'] = "[>notes<]";
//	form.refresh();

	openProfilePopup();


});

</script>
