<?php

/**
 * Delegated implementation of hook_civicrm_buildForm
 *
 * Customizes the UI for adding custom fields to allow the user to specify whether
 * a multi-select field should use the slider widget or not
 */
function _securefiles_civicrm_buildForm_CRM_Custom_Form_Field($formName, CRM_Core_Form &$form) {
  // set default value for the checkbox
  $field_id = $form->getVar('_id');
  $enabled_fields = _securefiles_get_secure_enabled_fields();
  $form->_defaultValues['use_securefiles'] = in_array($field_id, $enabled_fields);

  // add checkbox to the form object
  $form->add('checkbox', 'use_securefiles', ts('Store using SecureFiles'));

  // add checkbox/settings form to the display
  CRM_Core_Region::instance('page-body')->add(array(
    'template' => 'CRM/Securefiles/Form/CustomField.tpl',
  ));

  $secureFileElements = array();
  //Let the Backend service add fields if it wants/needs to
  $backendService = CRM_Securefiles_Backend::getBackendService();
  if($backendService) {
    $backendService->buildFieldSettingsForm($form, $secureFileElements);
  }
  //Assign our custom field names to the form so the template can render them.
  $form->assign("secureFileElements", $secureFileElements);

  // reposition and show/hide checkbox
  CRM_Core_Resources::singleton()->addScriptFile('com.ginkgostreet.securefiles', 'js/CRM_Custom_Form_Field.js');
}

/**
 * Delegated implementation of hook_civicrm_postProcess
 *
 * Handles the "Use Slider Widget?" field added to the custom fields UI
 */
function _securefiles_civicrm_postProcess_CRM_Custom_Form_Field($formName, &$form) {
  $use_securefiles = CRM_Utils_Array::value('use_securefiles', $form->_submitValues);
  $custom_field_id = $form->getVar('_id');

  $verb = $use_securefiles ? CRM_Core_Action::ADD : CRM_Core_Action::DELETE;
  _securefiles_update_enabled_fields(array($verb => $custom_field_id));
  //Allow the Backend service to save settings if it needs to
  $backendService = CRM_Securefiles_Backend::getBackendService();
  if($backendService) {
    $backendService->saveFieldSettings($form, $custom_field_id);
  }
}

/**
 * Delegated implementation of hook_civicrm_buildForm
 *
 * Registers the form to allow use of the volunteer slider widget.
 */
function _securefiles_civicrm_validateForm_CRM_Profile_Form_Edit($formName, &$fields, &$files, &$form, &$errors) {
  $backendService = CRM_Securefiles_Backend::getBackendService();
  if($backendService) {
    return $backendService->validateFieldSettings($formName, $fields, $files, $form, $errors);
  }
}

/**
 * For forms which have registered as slider-enabled, add the JS and CSS necessary
 * to render the slider widget(s).
 *
 * @param CRM_Core_Form $form
 */
function _securefiles_addWidgetToForm(CRM_Core_Form &$form) {
  $includeWidget = false;
  $enabled_fields = _securefiles_get_secure_enabled_fields();

  foreach ($form->_elements as &$field) {
    if($field->_type == "file") {
      $fieldId = str_replace("custom_", "", $field->_attributes['name']);
      $fieldId = preg_replace('/_.*/', "", $fieldId);
      if (in_array($fieldId, $enabled_fields)) {
        $css_classes = CRM_Utils_Array::value('class', $field->_attributes);
        $field->_attributes['class'] = trim($css_classes . ' securefiles_upload');
        $includeWidget = true;
      }
    }
  }


  if ($includeWidget) {
    $ccr = CRM_Core_Resources::singleton();
    $ccr->addScriptFile('com.ginkgostreet.securefiles', 'js/securefiles_widget.js');
    $ccr->addStyleFile('com.ginkgostreet.securefiles', 'css/securefiles_widget.css');

    $clientSideVars = array();
    $clientSideVars['currentContactId'] = CRM_Core_Session::singleton()->getLoggedInContactID();

    //Give the Backend Service a chance to add additional resources to the form.
    $backendService = CRM_Securefiles_Backend::getBackendService();
    if($backendService) {
      $backendService->runForm($form, $clientSideVars);
    }

    $ccr->addScript("CRM.$(function ($) { CRM.SecureFilesWidget = ".json_encode($clientSideVars)."; });", 1, 'page-body');
  }
}

/**
 * Helper function to get the list of fields IDs which have had the slider widget
 * applied to them.
 *
 * @return array
 */
function _securefiles_get_secure_enabled_fields() {
  return CRM_Core_BAO_Setting::getItem('securefiles', 'securefiles_enabled_fields', null, array());
}

/**
 * Add or remove fields from the slider widget datastore.
 *
 * @param array $params Arrays of custom field IDs which ought to be added or
 *              removed from the slider widget datastore, keyed by CRM_Core_Action::ADD for
 *              fields which should use the widget, and CRM_Core_Action::DELETE for fields which
 *              should not. If the same custom field ID appears in both the CRM_Core_Action::ADD
 *              and CRM_Core_Action::DELETE arrays, it will be removed.
 */
function _securefiles_update_enabled_fields(array $params) {
  $add = CRM_Utils_Array::value(CRM_Core_Action::ADD, $params, array());
  if (!is_array($add)) {
    $add = array($add);
  }

  $remove = CRM_Utils_Array::value(CRM_Core_Action::DELETE, $params, array());
  if (!is_array($remove)) {
    $remove = array($remove);
  }

  $enabled_fields = _securefiles_get_secure_enabled_fields();

  foreach ($add as $custom_field_id) {
    $enabled_fields[] = $custom_field_id;
  }
  $enabled_fields = array_unique($enabled_fields);

  foreach ($remove as $custom_field_id) {
    $key = array_search($custom_field_id, $enabled_fields);
    unset($enabled_fields[$key]);
  }

  sort($enabled_fields);
  CRM_Core_BAO_Setting::setItem($enabled_fields, 'securefiles', 'securefiles_enabled_fields');
}