<?php

class CRM_Securefiles_Hooks {

  static $_nullObject = NULL;

  /**
   * Hook that allows 3rd party extensions to register other backend services
   * for remote storage solutions
   *
   * @return array
   *  A list of backend services as key/value pairs
   *  {Class Name} => {Service Label}
   *  eg. "CRM_Securefiles_AmazonS3" => "Amazon S3"
   */
  public static function getBackendServices() {
    $services = array();
    CRM_Utils_Hook::singleton()->invoke(1, $services, self::$_nullObject, self::$_nullObject,
      self::$_nullObject, self::$_nullObject, self::$_nullObject,
      'Securefiles_getBackendServices'
    );
    return $services;
  }

  /**
   * Hook that allows 3rd party extensions alter the permissions
   * for a given user/file/action combo
   *
   *
   * @param $op
   *  The Action being requested
   * @param $file
   *  The File this action is being taken on
   * @param $user
   *  The user this file belongs to
   *
   * @return mixed
   *  (Bool) False for expressly deny operation
   *  (Bool) True for expressly allow this operation
   *  Null for fallback on built in permissions
   */
  public static function checkPermissions($op, $file, $user) {
    $valid = null;
    CRM_Utils_Hook::singleton()->invoke(4, $op, $file, $user, $valid,
      self::$_nullObject, self::$_nullObject,
      'Securefiles_alterFilePermissions'
    );
    return $valid;
  }
}