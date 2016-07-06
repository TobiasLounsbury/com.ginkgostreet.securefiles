<?php

require_once 'securefiles.civix.php';
require_once 'securefiles.widget.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function securefiles_civicrm_config(&$config) {
  _securefiles_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function securefiles_civicrm_xmlMenu(&$files) {
  _securefiles_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function securefiles_civicrm_install() {
  _securefiles_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function securefiles_civicrm_uninstall() {
  _securefiles_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function securefiles_civicrm_enable() {
  _securefiles_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function securefiles_civicrm_disable() {
  _securefiles_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function securefiles_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _securefiles_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function securefiles_civicrm_managed(&$entities) {
  _securefiles_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function securefiles_civicrm_caseTypes(&$caseTypes) {
  _securefiles_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function securefiles_civicrm_angularModules(&$angularModules) {
_securefiles_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function securefiles_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _securefiles_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function securefiles_civicrm_preProcess($formName, &$form) {

}

*/


/**
 * Implementation of hook_Securefiles_getBackendServices()
 * This hook allows 3rd party extensions to register back-end services
 * We are using it here to register Amazon S3
 *
 * @param $services
 */
function securefiles_Securefiles_getBackendServices(&$services) {
  $services['CRM_Securefiles_AmazonS3'] = "Amazon S3";
}


/**
 * Implementation of hook_civicrm_permission.
 *
 * @param array $permissions Does not contain core perms -- only extension-defined perms.
 */
function securefiles_civicrm_permission(array &$permissions) {
  CRM_Securefiles_Permission::getSecurefilePermissions($permissions);
}

/**
 * Implementation of hook_civicrm_navigationMenu.
 *
 * Adds Secure File Storage navigation items to the Administer menu.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function securefiles_civicrm_navigationMenu( &$params ) {
  // get the id of Administer Menu
  $administerMenuId = CRM_Core_DAO::getFieldValue('CRM_Core_BAO_Navigation', 'Administer', 'id', 'name');

  // skip adding menu if there is no administer menu
  if ($administerMenuId) {
    // get the maximum key under adminster menu
    $maxKey = max( array_keys($params[$administerMenuId]['child']));
    $params[$administerMenuId]['child'][$maxKey+1] =  array (
      'attributes' => array (
        'label'      => 'Secure File Storage',
        'name'       => 'SecureFileStorage',
        'url'        => 'civicrm/securefiles/settings?reset=1',
        'permission' => 'administer CiviCRM',
        'operator'   => NULL,
        'separator'  => false,
        'parentID'   => $administerMenuId,
        'navID'      => $maxKey+1,
        'active'     => 1
      )
    );
  }
}

/**
 * Implementation of hook_civicrm_buildForm
 *
 * Handler for buildForm hook.
 */
function securefiles_civicrm_buildForm($formName, &$form) {
  $f = '_' . __FUNCTION__ . '_' . $formName;
  if (function_exists($f)) {
    $f($formName, $form);
  }

  //This cuts out all the forms that couldn't have a file field
  if(array_key_exists("enctype", $form->_attributes) && $form->_attributes['enctype'] == "multipart/form-data") {
    _securefiles_addWidgetToForm($form);
  }
}

/**
 * Implementation of hook_civicrm_pageRun
 *
 * Handler for pageRun hook.
 */
function securefiles_civicrm_pageRun(&$page) {
  $f = '_' . __FUNCTION__ . '_' . get_class($page);
  if (function_exists($f)) {
    $f($page);
  }
}

/**
 * Implementation of hook_civicrm_postProcess
 *
 * Handler for postProcess hook.
 */
function securefiles_civicrm_postProcess($formName, &$form) {
  //_submitFiles is not empty
  $f = '_' . __FUNCTION__ . '_' . $formName;
  if (function_exists($f)) {
    $f($formName, $form);
  }

  if(array_key_exists("securefiles-metadata", $form->_submitValues) && $form->_submitValues['securefiles-metadata']) {
    $metadata = json_decode($form->_submitValues['securefiles-metadata']);
    if(!is_array($metadata)) {
      $metadata = array($metadata);
    }
    _securefiles_postprocessWidgetForm($metadata, $formName, $form);
  }

}

/**
 * Implementation of hook_civicrm_validateForm
 *
 * Handler for validateForm hook.
 */
function securefiles_civicrm_validateForm( $formName, &$fields, &$files, &$form, &$errors ) {
  $f = '_' . __FUNCTION__ . '_' . $formName;
  if (function_exists($f)) {
    $f( $formName, $fields, $files, $form, $errors );
  }

  if(array_key_exists("securefiles-metadata", $fields) && $fields['securefiles-metadata']) {
    $metadata = json_decode($fields['securefiles-metadata']);
    if(!is_array($metadata)) {
      $metadata = array($metadata);
    }
    _securefiles_validateWidgetForm($metadata, $formName, $fields, $files, $form, $errors );
  }
}
