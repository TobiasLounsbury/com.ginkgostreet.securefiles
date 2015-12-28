<?php

require_once 'securefiles.civix.php';

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
  // VOL-71: Until the Joomla/Civi integration is fixed, don't declare new perms
  // for Joomla installs
  if (CRM_Core_Config::singleton()->userPermissionClass->isModulePermissionSupported()) {

    $prefix = ts('SecureFileStorage', array('domain' => 'com.ginkgostreet.securefiles')) . ': ';
    $newPerms = array(
      'Administer SecureFileStorage' => array(
        $prefix . ts('Administer SecureFileStorage', array('domain' => 'com.ginkgostreet.securefiles')),
        ts('Administer global settings for SecureFile storage', array('domain' => 'com.ginkgostreet.securefiles')),
      ),
      'upload own secure files' => array(
        $prefix . ts('upload secure files', array('domain' => 'com.ginkgostreet.securefiles')),
        ts('Upload files to Secure Storage that are associated with own contact', array('domain' => 'com.ginkgostreet.securefiles')),
      ),
      'upload others secure files' => array(
        $prefix . ts('upload others secure files', array('domain' => 'com.ginkgostreet.securefiles')),
        ts('Upload Secure Files that are associated with another user for which this user has permissions', array('domain' => 'com.ginkgostreet.securefiles')),
      ),
      'view own secure files' => array(
        $prefix . ts('view own secure files', array('domain' => 'com.ginkgostreet.securefiles')),
        ts('Allows a user to download files associated with their contact record that they previously uploaded', array('domain' => 'com.ginkgostreet.securefiles')),
      ),
      'view all secure files' => array(
        $prefix . ts('view all secure files', array('domain' => 'com.ginkgostreet.securefiles')),
        ts('Allows a user to download  any secure files previously uploaded', array('domain' => 'com.ginkgostreet.securefiles')),
      ),
      'list own secure files' => array(
        $prefix . ts('list own secure files', array('domain' => 'com.ginkgostreet.securefiles')),
        ts('Allows a user to view a list of files they previously uploaded', array('domain' => 'com.ginkgostreet.securefiles')),
      ),
      'list all secure files' => array(
        $prefix . ts('list own secure files', array('domain' => 'com.ginkgostreet.securefiles')),
        ts('Allows a user to view a list of all secure files', array('domain' => 'com.ginkgostreet.securefiles')),
      ),
    );

    $permissions = array_merge($permissions, $newPerms);
  }
}