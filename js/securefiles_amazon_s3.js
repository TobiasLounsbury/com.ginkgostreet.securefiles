CRM.$(function ($) {

  $("#securefiles_s3_use_encryption").change(function(e) {
    if($(this).is(":checked")) {
      $(".securefiles_s3_encryption_type-section").slideDown();
      if($("#securefiles_s3_encryption_type").val() === "SSE-C") {
        $(".securefiles_s3_encryption_cert-section").slideDown();
      }
    } else {
      $(".securefiles_s3_encryption_type-section").slideUp();
      $(".securefiles_s3_encryption_cert-section").slideUp();
    }
  });

  $("#securefiles_s3_encryption_type").change(function(e) {
    if ($(this).val() === "SSE-C") {
      $(".securefiles_s3_encryption_cert-section").slideDown();
    } else {
      $(".securefiles_s3_encryption_cert-section").slideUp();
    }
  });


  $("#securefiles_s3_use_encryption").change();

});