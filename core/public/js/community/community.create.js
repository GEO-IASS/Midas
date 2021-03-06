// Midas Server. Copyright Kitware SAS. Licensed under the Apache License 2.0.

var midas = midas || {};
var nameValid = false;

$('form.createCommunityForm').submit(function () {
    'use strict';
    return nameValid;
});

$('div.createNameElement input').focusout(function () {
    'use strict';
    $.post($('.webroot').val() + '/community/validentry', {
        entry: $('input[name=name]').val(),
        type: 'dbcommunityname'
    },
        function (data) {
            if (data.search('true') != -1) {
                midas.createNotice('Name already exists', 4000);
                nameValid = false;
            }
            else {
                nameValid = true;
            }
        });
});

initCommunityPrivacy();

function initCommunityPrivacy() {
    'use strict';
    if ($('input[name=privacy]:checked').val() == 1) // private
    {
        $('input[name=canJoin]').attr('disabled', 'disabled');
        $('input[name=canJoin]').removeAttr('checked');
        $('input[name=canJoin][value=0]').attr('checked', true); // invitation
        $('div#canJoinDiv').hide();
    }
    else {
        $('input[name=canJoin]').removeAttr('disabled');
        $('input[name=canJoin]').removeAttr('checked');
        $('input[name=canJoin][value=1]').attr('checked', true); // invitation
        $('div#canJoinDiv').show();
    }
    $('input[name=privacy]').change(function () {
        initCommunityPrivacy();
    });
}
