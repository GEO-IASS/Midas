$( "#tabsSettings" ).tabs();

$( "#tabsSettings" ).css('display','block');
$( "#tabsSettings" ).show();

$('#modifyPassword').ajaxForm( { beforeSubmit: validatePasswordChange, success:       successPasswordChange  } );

$('#modifyAccount').ajaxForm( { beforeSubmit: validateAccountChange, success:       successAccountChange  } );

$('#modifyPicture').ajaxForm( { beforeSubmit: validatePictureChange, success:       successPictureChange  } );

jsonSettings = jQuery.parseJSON($('div.jsonSettingsContent').html());

function validatePasswordChange(formData, jqForm, options) { 
 
    var form = jqForm[0]; 
    if (form.newPassword.value.length<2)
      {
        createNotive(jsonSettings.passwordErrorShort,4000);
        return false;
      }
    if (form.newPassword.value.length<2||form.newPassword.value != form.newPasswordConfirmation.value) { 
        createNotive(jsonSettings.passwordErrorMatch,4000);
        return false;
    } 
}

function validatePictureChange(formData, jqForm, options) { 
 
    var form = jqForm[0]; 

}

function validateAccountChange(formData, jqForm, options) { 
 
    var form = jqForm[0]; 
    if (form.firstname.value.length<1)
      {
        createNotive(jsonSettings.accountErrorFirstname,4000);
        return false;
      }
    if (form.lastname.value.length<1)
      {
        createNotive(jsonSettings.accountErrorLastname,4000);
        return false;
      }
}

function successPasswordChange(responseText, statusText, xhr, $form) 
{
  jsonResponse = jQuery.parseJSON(responseText);
  if(jsonResponse==null)
    {
      createNotive('Error',4000);
      return;
    }
  if(jsonResponse[0])
    {
      createNotive(jsonResponse[1],4000);
    }
  else
    {
      $('#modifyPassword input[type=password]').val(''); 
      createNotive(jsonResponse[1],4000);
    }
}

function successAccountChange(responseText, statusText, xhr, $form) 
{
  jsonResponse = jQuery.parseJSON(responseText);
  if(jsonResponse==null)
    {
      createNotive('Error',4000);
      return;
    }
  if(jsonResponse[0])
    {
      $('a#topUserName').html($('#modifyAccount input[name=firstname]').val()+' '+$('#modifyAccount input[name=lastname]').val()+' <img class="arrowUser" src="'+json.global.coreWebroot+'/public/images/icons/arrow-user.gif" alt ="" />');
      createNotive(jsonResponse[1],4000);
    }
  else
    {
      createNotive(jsonResponse[1],4000);
    }
}

function successPictureChange(responseText, statusText, xhr, $form) 
{
  jsonResponse = jQuery.parseJSON(responseText);
  if(jsonResponse==null)
    {
      createNotive('Error',4000);
      return;
    }
  if(jsonResponse[0])
    {
       $('img#userTopThumbnail').attr('src',json.global.webroot+'/'+jsonResponse[2]);
       createNotive(jsonResponse[1],4000);
    }
  else
    {
      createNotive(jsonResponse[1],4000);
    }
}