var midas = midas || {};
midas.community = midas.community || {};
midas.community.manage = {};

var disableElementSize=true;

midas.community.manage.init = function()
{
  var mainDialogContentDiv = $('div.MainDialogContent');
  var createGroupFromDiv = $('div#createGroupFrom');

  $('a#createGroupLink').click(function() {
    $('div.groupUsersSelection').hide();
    $('td.tdUser input').removeAttr('checked');
    mainDialogContentDiv.html('');
    createGroupFromDiv.find('input[name=groupId]').val('0');
    createGroupFromDiv.find('input[name=name]').val('');
    showDialogWithContent(json.community.message.createGroup, createGroupFromDiv.html(), false);
    mainDialogContentDiv.find('form.editGroupForm').ajaxForm({
      beforeSubmit: midas.community.manage.validateGroupChange,
      success: midas.community.manage.successGroupChange
      });
    });

  $('a.editGroupLink').click(function() {
    mainDialogContentDiv.html('');
    var id = $(this).attr('groupid');
    createGroupFromDiv.find('input[name=groupId]').val(id);
    var groupName=$(this).parent('li').find('a:first').html();
    showDialogWithContent(json.community.message.editGroup,createGroupFromDiv.html(), false);
    $('form.editGroupForm input#name').val(groupName);
    mainDialogContentDiv.find('form.editGroupForm').ajaxForm({
      beforeSubmit: midas.community.manage.validateGroupChange,
      success: midas.community.manage.successGroupChange
      });
    });

  $('a.deleteGroupLink').click(function() {
    var html='';
    html += json.community.message['deleteGroupMessage'];
    html += '<br/>';
    html += '<br/>';
    html += '<br/>';
    html += '<input style="margin-left:140px;" class="globalButton deleteGroupYes" element="'+$(this).attr('groupid')+'" type="button" value="'+json.global.Yes+'"/>';
    html += '<input style="margin-left:50px;" class="globalButton deleteGroupNo" type="button" value="'+json.global.No+'"/>';

    showDialogWithContent(json.community.message['delete'],html,false);

    $('input.deleteGroupYes').unbind('click').click(function() {
      var groupid = $(this).attr('element');
      $.post(json.global.webroot+'/community/manage', {communityId: json.community.community_id, deleteGroup: 'true', groupId:groupid}, function(data) {
        var jsonResponse = jQuery.parseJSON(data);
        if(jsonResponse == null)
          {
          createNotice('Error', 4000);
          return;
          }
        if(jsonResponse[0])
          {
          $("div.MainDialog").dialog("close");
          $('a.groupLink[groupid='+groupid+']').parent('li').remove();
          createNotice(jsonResponse[1], 4000);
          midas.community.manage.init();
          window.location.replace(json.global.webroot+'/community/manage?communityId='+json.community['community_id']+'#tabs-2');
          window.location.reload();
          }
        else
          {
          createNotice(jsonResponse[1], 4000);
          }
        });
      });
    $('input.deleteGroupNo').unbind('click').click(function() {
      $( "div.MainDialog" ).dialog('close');
      });
    });
}

midas.community.manage.initDragAndDrop = function()
{
  $("#browseTable .file, #browseTable .folder:not(.notdraggable)").draggable({
    helper: "clone",
    cursor: "move",
    opacity: .75,
    refreshPositions: true, // Performance?
    revert: "invalid",
    revertDuration: 300,
    scroll: true,
    start: function() {
      $('div.userPersonalData').show();
      }
    });

  // Configure droppable rows
  $("#browseTable .folder").each(function() {
    $(this).parents("tr").droppable({
      accept: ".file, .folder",
      drop: function(e, ui) {
        // Call jQuery treeTable plugin to move the branch
        var elements='';
        if($(ui.draggable).parents("tr").attr('type') == 'folder')
          {
          elements = $(ui.draggable).parents("tr").attr('element')+';';
          }
        else
          {
          elements = ';'+$(ui.draggable).parents("tr").attr('element');
          }
        var from_ojbect;
        var classNames=$(ui.draggable).parents("tr").attr('class').split(' ');
        for(key in classNames) {
          if(classNames[key].match('child-of-')) {
            from_obj = "#" + classNames[key].substring(9);
            }
          }
        var destination_obj=this;

        // do nothing if drop item(s) to its current folder
        if($(this).attr('id') != $(from_obj).attr('id')) {
          $.post(json.global.webroot+'/browse/movecopy',
                 {moveElement: true, elements: elements , destination:$(this).attr('element'),from:$(from_obj).attr('element'),ajax:true},
                 function(data) {
            var jsonResponse = jQuery.parseJSON(data);
            if(jsonResponse==null)
              {
              createNotice('Error', 4000);
              return;
              }
            if(jsonResponse[0])
              {
              createNotice(jsonResponse[1], 1500);
              $($(ui.draggable).parents("tr")).appendBranchTo(destination_obj);
              }
            else
              {
              createNotice(jsonResponse[1], 4000);
              }
            });
          }
        },
      hoverClass: "accept",
      over: function(e, ui) {
        // Make the droppable branch expand when a draggable node is moved over it.
        if(this.id != $(ui.draggable.parents("tr")[0]).id && !$(this).is(".expanded")) {
          $(this).expand();
          }
        }
      });
    });
}

midas.community.manage.validateGroupChange = function (formData, jqForm, options)
{
  var form = jqForm[0];
  if(form.name.value.length < 1)
    {
    createNotice(json.community.message.infoErrorName, 4000);
    return false;
    }
}

midas.community.manage.successGroupChange = function(responseText, statusText, xhr, form)
{
  $("div.MainDialog").dialog("close");
  var jsonResponse = jQuery.parseJSON(responseText);
  if(jsonResponse == null)
    {
    createNotice('Error',4000);
    return;
    }
  if(jsonResponse[0])
    {
    createNotice(jsonResponse[1], 4000);
    var obj = $('a.groupLink[groupId='+jsonResponse[2].group_id+']');
    if(obj.length > 0)
      {
      obj.html(jsonResponse[2].name);
      }

    midas.community.manage.init();
    window.location.replace(json.global.webroot+'/community/manage?communityId='+json.community['community_id']+'#tabs-2');
    window.location.reload();
    }
  else
    {
    createNotice(jsonResponse[1],4000);
    }
}

midas.community.manage.validateInfoChange = function(formData, jqForm, options)
{
  var form = jqForm[0];
  if(form.name.value.length < 1)
    {
    createNotice(json.community.message.infoErrorName, 4000);
    return false;
    }
}

midas.community.manage.successInfoChange = function(responseText, statusText, xhr, form)
{
  var jsonResponse = jQuery.parseJSON(responseText);
  if(jsonResponse == null)
    {
    createNotice('Error', 4000);
    return;
    }
  if(jsonResponse[0])
    {
    $('div.genericName').html(jsonResponse[2]);
    createNotice(jsonResponse[1], 4000);
    }
  else
    {
    createNotice(jsonResponse[1], 4000);
    }
}

midas.community.manage.promoteMember = function(userId)
{
  loadDialog('promoteId'+userId+'.'+
             json.community.community_id+
             new Date().getTime(),
             '/community/promotedialog?user='+userId+'&community='+json.community.community_id);
  showDialog('Add user to groups', false);
}

midas.community.manage.removeFromGroup = function(userId, groupId)
{
  $.post(json.global.webroot+'/community/removeuserfromgroup', {groupId: groupId, userId: userId}, function(data) {
    jsonResponse = jQuery.parseJSON(data);
    if(jsonResponse == null)
      {
      createNotice('Error', 4000);
      return;
      }
    createNotice(jsonResponse[0], 4000);
    if(jsonResponse[0])
      {
      window.location.replace(json.global.webroot+'/community/manage?communityId='+
                              json.community.community_id+'#tabs-2');
      window.location.reload();
      }
    });
}

/** Used to remove a user from the members group, and thus all other groups */
midas.community.manage.removeMember = function(userId, groupId)
{
  var html='';
  html += 'Are you sure you want to remove the user from this community? They will be removed from all groups.';
  html += '<br/>';
  html += '<br/>';
  html += '<br/>';
  html += '<span style="float: right">';
  html += '<input class="globalButton removeUserYes" type="button" value="'+json.global.Yes+'"/>';
  html += '<input style="margin-left:15px;" class="globalButton removeUserNo" type="button" value="'+json.global.No+'"/>';

  showDialogWithContent('Remove user from community', html, false);
  $('input.removeUserYes').unbind('click').click(function() {
    midas.community.manage.removeFromGroup(userId, groupId);
    });
  $('input.removeUserNo').unbind('click').click(function() {
    $( "div.MainDialog" ).dialog('close');
    alert('user not deleted');
    });
}

midas.community.manage.initCommunityPrivacy = function()
{
  var inputCanJoin = $('input[name=canJoin]');
  var inputPrivacy = $('input[name=privacy]');
  var canJoinDiv = $('div#canJoinDiv');

  if(inputPrivacy.filter(':checked').val() == 1) //private
    {
    inputCanJoin.attr('disabled', 'disabled');
    inputCanJoin.removeAttr('checked');
    inputCanJoin.filter('[value=0]').attr('checked', true); //invitation
    canJoinDiv.hide();
    }
  else
    {
    inputCanJoin.removeAttr('disabled');
    canJoinDiv.show();
    }
  inputPrivacy.change(function(){
  midas.community.manage.initCommunityPrivacy();
  });
}

$(document).ready(function() {

  midas.community.manage.initCommunityPrivacy();

  $("#tabsGeneric").tabs({
    select: function(event, ui) {
      $('div.genericAction').show();
      $('div.genericCommunities').show();
      $('div.genericStats').show();
      $('div.viewInfo').hide();
      $('div.memberSelection').hide();
      $('div.groupUsersSelection').hide();
      $('div.viewAction').hide();
      $('td.tdUser input').removeAttr('checked');
      }
    });
  $("#tabsGeneric").show();
  $('img.tabsLoading').hide();


  $('a#communityDeleteLink').click(function() {
    var html='';
    html += json.community.message['deleteMessage'];
    html += '<br/>';
    html += '<br/>';
    html += '<br/>';
    html += '<input style="margin-left:140px;" class="globalButton deleteCommunityYes" element="'+$(this).attr('element')+'" type="button" value="'+json.global.Yes+'"/>';
    html += '<input style="margin-left:50px;" class="globalButton deleteCommunityNo" type="button" value="'+json.global.No+'"/>';

    showDialogWithContent(json.community.message['delete'], html, false);

    $('input.deleteCommunityYes').unbind('click').click(function() {
      location.replace(json.global.webroot+'/community/delete?communityId='+json.community.community_id);
      });
    $('input.deleteCommunityNo').unbind('click').click(function() {
      $( "div.MainDialog" ).dialog('close');
      });
    });

    $('#editCommunityForm').ajaxForm({
      beforeSubmit: midas.community.manage.validateInfoChange,
      success: midas.community.manage.successInfoChange
      });

    //init group tab
    midas.community.manage.init();
    $('table.tablesorter').tablesorter({
      widgets: ['zebra'],
      headers: {
        1: {sorter: false} // Actions column not sortable
        }
    });

    //init tree
    $('img.tabsLoading').hide()


     $('table')
        .filter(function() {
            return this.id.match(/browseTable*/);
        })
        .treeTable();
    ;
    $("img.tableLoading").hide();
    $("table#browseTable").show();

    $('div.userPersonalData').hide();

    midas.community.manage.initDragAndDrop();
    $('td.tdUser input').removeAttr('checked');
  });


//depends on common.browser.js
var ajaxSelectRequest='';
function callbackSelect(node)
  {
  $('div.genericAction').show();
  $('div.genericCommunities').hide();
  $('div.genericStats').hide();
  $('div.viewInfo').show();
  $('div.viewAction').show()
  genericCallbackSelect(node);
  }

function callbackDblClick(node)
  {
  }

function callbackCheckboxes(node)
  {
  genericCallbackCheckboxes(node);
  }

function callbackCreateElement(node)
  {
  midas.community.manage.initDragAndDrop();
  }
