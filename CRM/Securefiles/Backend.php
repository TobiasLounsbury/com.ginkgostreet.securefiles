<?php

abstract class CRM_Securefiles_Backend {

  /*-------[ Abstract functions the backend service must implement ]-------*/

  abstract public function uploadFile($file, $user = null);
  abstract public function downloadFile($file, $user = null);
  abstract public function deleteFile($file, $user = null);
  abstract public function listFiles($user = null);
  abstract public function fileMetadata($file, $field);


  /**
   * This method is here to do server-side cleanup
   * once the file is securely stored.
   */
  function cleanup() {}






  /*--------[ Below are methods for interacting with the settings form ]--------*/

  /**
   * This form allows the Backend Service to
   * add custom fields for its configuration
   * to the SecureFiles settings form
   *
   * Delegated from CRM_Securefiles_Form_Settings::buildQuickForm
   *
   * @param $form
   * An instance of the Settings form being created
   */
  function buildSettingsForm(&$form) {}


  /**
   * This functions returns the values for the settings form
   *
   * Delegated from CRM_Securefiles_Form_Settings::setDefaultValues
   *
   * @param $defaults
   */
  function defaultSettings(&$defaults) {}


  /**
   * This function saves the values submitted via
   *  The SecureFiles settings form
   *
   *  Delegated from CRM_Securefiles_Form_Settings::postProcess
   *
   * @param $values
   *  The values submitted via the Securefiles settings form
   */
  function saveSettings($values) {}

  /**
   * This form validates the settings specific to this
   * Backend Service
   *
   * Delegated from CRM_Securefiles_Form_Settings::validate
   *
   * @param $form
   * An instance of the form that is being validated
   * @return bool
   * Whether this form is valid
   */
  function validateSettings(&$form) { return true;}







  /*--------[ Below are the CustomField specific settings ]--------*/


  /**
   * This form allows the Backend Service to
   * add custom fields for its configuration
   * to the the config for the individual custom fields
   *
   * Delegated from _securefiles_civicrm_buildForm_CRM_Custom_Form_Field
   * which is an instance of hook_civicrm_buildForm
   *
   * @param $form
   * An instance of the Settings form being created
   * @param $fieldNames
   * An array of fieldnames that will be auto-rendered by the
   * template
   */
  function buildFieldSettingsForm(&$form, &$fieldNames) {}

  /**
   * This method allows the backend to save the custom
   * settings it added to the field config
   *
   * Delegated from _securefiles_civicrm_postProcess_CRM_Custom_Form_Field
   * which is delegated from an instance of hook_civicrm_postProcess
   *
   * @param $form
   *  An instance of the Settings form for this custom field
   * @param $fieldId
   * The id of the custom field being edited/created
   */
  function saveFieldSettings(&$form, $fieldId) {}


  /**
   * This method allow the backend service to validate
   * the values of the settings it added to the custom field config
   *
   * Delegated from _securefiles_civicrm_validateForm_CRM_Profile_Form_Edit
   * Which an instance of hook_civicrm_validateForm
   *
   * @param $formName
   * @param $fields
   * @param $files
   * @param $form
   * @param $errors
   * @return bool
   */
  function validateFieldSettings( $formName, &$fields, &$files, &$form, &$errors ) {return true;}


  /**
   * This method is called when a form with a configured
   * secureFiles field is being created and allows
   * the backend service to add fields/or resources
   * as needed to accomplish its tasks
   *
   * Delegated from _securefiles_addWidgetToForm
   * which is an implementation of hook_civicrm_buildForm
   *
   * @param $form
   * An instance of the form that is being created
   */
  function runForm( &$form, &$clientSideVars, $fields) {}


  /**
   * Validate a form that was submitted with securefile enabled widgets
   *
   * Delegated from _securefiles_validateWidgetForm
   * which is an implementation of hook_civicrm_validateForm
   *
   * @param $metadata
   * @param $formName
   * @param $fields
   * @param $files
   * @param $form
   * @param $errors
   */
  function validateWidgetForm($metadata, $formName, &$fields, &$files, &$form, &$errors) {}


  /**
   * Post process a form that was submitted with a securefiles enabled widget
   *
   * Delegated from _securefiles_postProcessWidgetForm
   * which is an implementation of hook_civicrm_postProcess
   *
   * @param $metadata
   * The decoded metadata submitted via the front end.
   * @param $formName
   * @param $form
   */
  function postProcessWidgetForm($metadata, $formName, &$form) {}





  /*--------[ Below are Static functions for global use ]--------*/


  /**
   * This method looks for and creates an instance of the
   * configured backend service class.
   *
   * @return mixed
   * Either an instance of the configured Backend Service or
   * false if that class is not in scope/available
   */
  static function getBackendService() {
    $backend_service_class = CRM_Core_BAO_Setting::getItem("securefiles", "securefiles_backend_service", null, "CRM_Securefiles_AmazonS3");

    if(class_exists($backend_service_class)) {
      return new $backend_service_class();
    } else {
      return false;
    }
  }

}