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
}