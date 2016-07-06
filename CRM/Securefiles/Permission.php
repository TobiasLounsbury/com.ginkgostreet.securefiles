<?php


class CRM_Securefiles_Permission extends CRM_Core_Permission {

  const LIST_SECURE_FILES = 'securefiles_list_files'; // A number unused by CRM_Core_Action

  public static function getSecurefilePermissions(&$permissions) {
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
        'delete own secure files' => array(
          $prefix . ts('delete own secure files', array('domain' => 'com.ginkgostreet.securefiles')),
          ts('Allows a user to delete files they previously uploaded', array('domain' => 'com.ginkgostreet.securefiles')),
        ),
        'delete all secure files' => array(
          $prefix . ts('delete all secure files', array('domain' => 'com.ginkgostreet.securefiles')),
          ts('Allows a user to delete secure files for any user', array('domain' => 'com.ginkgostreet.securefiles')),
        ),
      );

      $permissions = array_merge($permissions, $newPerms);
    }
  }


  public static function checkFilePerms($op, $file, $user) {

    $opsRequiringProjectId = array(CRM_Core_Action::UPDATE, CRM_Core_Action::DELETE);
    if (in_array($op, $opsRequiringProjectId) && empty($projectId)) {
      CRM_Core_Error::fatal('Missing required parameter Project ID');
    }

    //Run the hook that allows third party extensions to
    //Alter the permissions of a file operation.
    //If true, they have permission
    //If False, they expressly do not
    //If null, fallback on the following checks.
    $validByHook = CRM_Securefiles_Hooks::checkPermissions($op, $file, $user);
    if(!is_null($validByHook)) {
      return $validByHook;
    }

    $contactId = CRM_Core_Session::getLoggedInContactID();
    $checkUserRelationship = !($contactId == $user);

    switch ($op) {
      case CRM_Core_Action::ADD:
      case CRM_Core_Action::UPDATE:
        if ($checkUserRelationship) {
          return self::check('upload others secure files');
          //Todo: Check relationships and allow for permissioned relationships
        } else {
          return self::check('upload own secure files');
        }
      break;
      case CRM_Core_Action::DELETE:
        if ($checkUserRelationship) {
          return self::check("delete all secure files");
          //Todo: Check relationships and allow for permissioned relationships
        } else {
          return self::check("delete own secure files");
        }
        break;
      case CRM_Core_Action::VIEW:
        if ($checkUserRelationship) {
          return self::check('view all secure files');
          //Todo: Check relationships and allow for permissioned relationships
        } else {
          return self::check('view own secure files');
        }
        break;
      case self::LIST_SECURE_FILES:
        if ($checkUserRelationship) {
          return self::check('list all secure files');
          //Todo: Check relationships and allow for permissioned relationships
        } else {
          return self::check('list own secure files');
        }
        break;
    }

    return FALSE;
  }

}