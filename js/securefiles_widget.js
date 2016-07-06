CRM.$(function ($) {

  if(!CRM.SecureFiles) {
    CRM.SecureFiles = {};
  }


  //Add a metadata Field:
  $(".securefiles_upload").closest("form").append("<input type='hidden' class='securefiles-metadata' name='securefiles-metadata' />");

});