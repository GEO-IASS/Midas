var midas = midas || {};
midas.browse = midas.browse || {};

//dependance: common/browser.js
midas.ajaxSelectRequest='';
midas.browse.moveCopyCallbackSelect = function (node) {
    var selectedElement = node.find('span:eq(1)').html();
    var parent = true;
    var current = node;

    while(parent != null) {
        parent = null;
        var classNames = current[0].className.split(' ');
        for(key in classNames) {
            if(classNames[key].match("child-of-")) {
                parent = $("#moveCopyTable #" + classNames[key].substring(9));
            }
        }
        if(parent != null) {
            selectedElement = parent.find('span:eq(1)').html()+'/'+selectedElement;
            current = parent;
        }
    }

    $('#selectedDestinationHidden').val(node.attr('element'));
    $('#selectedDestination').html(sliceFileName(selectedElement, 40));
    if(node.attr('valid') == 'false' || parseInt(node.attr('policy')) < 1) {
        $('#selectElement').attr('disabled', 'disabled');
        $('#shareElement').attr('disabled', 'disabled');
        $('#duplicateElement').attr('disabled', 'disabled');
        $('#moveElement').attr('disabled', 'disabled');
    }
    else {
        $('#selectElement').removeAttr('disabled');
        $('#shareElement').removeAttr('disabled');
        $('#duplicateElement').removeAttr('disabled');
        $('#moveElement').removeAttr('disabled');
    }
};


midas.browse.moveCopyCallbackDblClick = function (node) {
    //  midas.genericCallbackDblClick(node);
};

midas.browse.moveCopyCallbackCheckboxes = function (node) {
    //  midas.genericCallbackCheckboxes(node);
};

midas.browse.moveCopyCallbackCustomElements = function (node,elements,first) {
    var i = 1;
    var id = node.attr('id');
    elements['folders'] = jQuery.makeArray(elements['folders']);
    var padding=parseInt(node.find('td:first').css('padding-left').slice(0,-2));
    var html='';
    $.each(elements.folders, function(index, value) {
        html+= "<tr id='"+id+"-"+i+"' class='parent child-of-"+id+"' ajax='"+value['folder_id']+"'type='folder'  policy='"+value['policy']+"' element='"+value['folder_id']+"'>";
        html+= "  <td><span class='folder'>"+trimName(value['name'],padding)+"</span></td>";
        html+= "</tr>";
        i++;
    });
    return html;
};

$(document).ready(function () {
    $('#moveCopyForm').submit(function () {
        $('img.submitWaiting').show();
        return true;
    });

    $("#moveCopyTable").treeTable({
        callbackSelect: midas.browse.moveCopyCallbackSelect,
        callbackCheckboxes: midas.browse.moveCopyCallbackCheckboxes,
        callbackDblClick: midas.browse.moveCopyCallbackDblClick,
        callbackCustomElements: midas.browse.moveCopyCallbackCustomElements,
        pageLength: 99999 // do not page this table (preserves old functionality)
    });
    $("img.tableLoading").hide();
    $("table#moveCopyTable").show();

    $('.uploadApplet').hide();

    if($('#selectElement') != undefined) {
        $('#selectElement').click(function () {
            var destHtml = $('#selectedDestination').html();
            var destValue = $('#selectedDestinationHidden').val();
            $('#destinationUpload').html(destHtml);
            $('#destinationId').val(destValue);
            $('.destinationUpload').html(destHtml);
            $('.destinationId').val(destValue);
            $( "div.MainDialog" ).dialog('close');
            $('.uploadApplet').show();
            return false;
        });
    }
});
