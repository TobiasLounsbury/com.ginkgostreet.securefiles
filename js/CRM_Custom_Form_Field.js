(function(ts) {
  CRM.$(function($) {
    var widgetEnabledSection = $(".securefiles-custom-field-enabled-section");
    var widgetSettingsSection = $('.securefiles-custom-field-settings');
    var data_type_sel = $('#data_type_0');

    // move the settings container where we want it
    $('#Field .crm-custom-field-form-block-is_required').before(widgetEnabledSection);
    widgetEnabledSection.after(widgetSettingsSection)


    widgetEnabledSection.change(function() {
      widgetSettingsSection.toggle( ($(this).is(":checked")) );
    });


    // wire up the field for HTML type to display the settings
    data_type_sel.change(function (){
      widgetEnabledSection.toggle(($('#data_type_1').val() === 'File')).change();
    });

    //Trigger changes so the proper fields are visible at load time.
    data_type_sel.trigger('change');

    //Cleanup
    $("#securefiles-throwaway").remove();
  });
}(CRM.ts('com.ginkgostreet.securefiles')));