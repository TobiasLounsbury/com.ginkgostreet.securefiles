CRM.$(function ($) {

  if(!CRM.SecureFiles) {
    CRM.SecureFiles = {};
  }

  $("#securefiles_backend_service").change(function(e) {
    if($(this).val() !== CRM.SecureFiles.Backend) {
      $(".securefiles-backend-service-settings-section").slideUp();
      //Let the user know why we are hiding the settings
      CRM.alert("You must save changes to the backend service before it's settings will be available.", "Warning", "warning");
    } else {
      $(".securefiles-backend-service-settings-section").slideDown();
    }
  });



  CRM.SecureFiles.Backend = $("#securefiles_backend_service").val()

});