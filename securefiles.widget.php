<?php

/**
 * Delegated implementation of hook_civicrm_buildForm
 *
 * Customizes the UI for adding custom fields to allow the user to specify whether
 * this field should be handled by the secure file extension
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
 * Handles the "Store using SecureFiles?" field added to the custom fields UI
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
 * Delegated implementation of hook_civicrm_validateForm
 *
 * Validate the field settings form
 */
function _securefiles_civicrm_validateForm_CRM_Custom_Form_Field($formName, &$fields, &$files, &$form, &$errors) {
  $backendService = CRM_Securefiles_Backend::getBackendService();
  if($backendService) {
    return $backendService->validateFieldSettings($formName, $fields, $files, $form, $errors);
  }
}

/**
 * Check to see if we have any enabled fields on this
 * form, and if so, set the required class, add them
 * to the list, notify the backend service, and add
 * the required JS and CSS to the page.
 *
 * @param CRM_Core_Form $form
 */
function _securefiles_addWidgetToForm(&$form) {
  $includeWidget = false;
  $enabled_fields = _securefiles_get_secure_enabled_fields();
  $secureFields = array();

  foreach ($form->_elements as $index => &$field) {
    if($field->_type == "file") {
      $fieldId = str_replace("custom_", "", $field->_attributes['name']);
      $fieldId = preg_replace('/_.*/', "", $fieldId);
      if (in_array($fieldId, $enabled_fields)) {
        $secureFields[$index] = $fieldId;
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
      $backendService->runForm($form, $clientSideVars, $secureFields);
    }

    $ccr->addScript("CRM.$(function ($) { CRM.SecureFilesWidget = ".json_encode($clientSideVars)."; });", 1, 'page-body');
  }
}

/**
 * This function allows the backend service to
 * take action after a form has been submitted
 * with securefiles enabled widgets
 *
 * Delegated from: securefiles_civicrm_postProcess
 *
 * @param $metadata
 *  The metadata that was submittted via this form
 * @param $formName
 *  The name of the Form that is being Processed
 * @param $form
 *  A reference to the form object
 */
function _securefiles_postProcessWidgetForm($metadata, $formName, &$form) {

  //Give the Backend Service a chance deal with submission
  $backendService = CRM_Securefiles_Backend::getBackendService();
  if($backendService) {
    $backendService->postProcessWidgetForm($metadata, $formName, $form);
  }

}

/**
 * This function allows the backend service
 * to do form validation on a form that was
 * submitted with securefiles enabled widgets
 *
 * Delegated from: securefiles_civicrm_validateForm
 *
 * @param $metadata
 * Metadata for the form that was submitted
 * @param $formName
 * @param $fields
 * @param $files
 * @param $form
 * @param $errors
 */
function _securefiles_validateWidgetForm($metadata, $formName, &$fields, &$files, &$form, &$errors) {
  //Give the Backend Service a chance validate submission
  $backendService = CRM_Securefiles_Backend::getBackendService();
  if($backendService) {
    $backendService->validateWidgetForm($metadata, $formName, $fields, $files, $form, $errors);
  }
}

/**
 * Helper function to get the list of fields IDs which have
 * securefiles enabled as the upload handler
 *
 * @return array
 */
function _securefiles_get_secure_enabled_fields() {
  return CRM_Core_BAO_Setting::getItem('securefiles', 'securefiles_enabled_fields', null, array());
}

/**
 * Add or remove fields from the securefiles field settings
 *
 * @param array $params Arrays of custom field IDs which ought to be added or
 *              removed from the securefiles settings, keyed by CRM_Core_Action::ADD for
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