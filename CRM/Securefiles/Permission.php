<?php


class CRM_Securefiles_Permission extends CRM_Core_Permission {


  public static function getSecurefilePermissions(&$permissions) {
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


  public static function checkFilePerms($op, $file, $user) {

    /*
    $opsRequiringProjectId = array(CRM_Core_Action::UPDATE, CRM_Core_Action::DELETE);
    if (in_array($op, $opsRequiringProjectId) && empty($projectId)) {
      CRM_Core_Error::fatal('Missing required parameter Project ID');
    }

    $contactId = CRM_Core_Session::getLoggedInContactID();

    switch ($op) {
      case CRM_Core_Action::ADD:
        return self::check('create volunteer projects');

      case CRM_Core_Action::UPDATE:
        if (self::check('edit all volunteer projects')) {
          return TRUE;
        }

        $projectOwners = CRM_Volunteer_BAO_Project::getContactsByRelationship($projectId, 'volunteer_owner');
        if (self::check('edit own volunteer projects')
          && in_array($contactId, $projectOwners)) {
          return TRUE;
        }
        break;
      case CRM_Core_Action::DELETE:
        if (self::check('delete all volunteer projects')) {
          return TRUE;
        }

        $projectOwners = CRM_Volunteer_BAO_Project::getContactsByRelationship($projectId, 'volunteer_owner');
        if (self::check('delete own volunteer projects')
          && in_array($contactId, $projectOwners)) {
          return TRUE;
        }
        break;
      case CRM_Core_Action::VIEW:
        if (self::check('register to volunteer') || self::check('edit all volunteer projects')) {
          return TRUE;
        }
        break;
      case self::VIEW_ROSTER:
        if (self::check('edit all volunteer projects')) {
          return TRUE;
        }

        $projectManagers = CRM_Volunteer_BAO_Project::getContactsByRelationship($projectId, 'volunteer_manager');
        if (in_array($contactId, $projectManagers)) {
          return TRUE;
        }
        break;
    }

    return FALSE;
  }
*/

}