<?php

abstract class CRM_Securefiles_Backend {

  abstract protected function uploadFile($file, $user = null);
  abstract protected function downloadFile($file, $user = null);
  abstract protected function deleteFile($file, $user = null);
  abstract protected function listFiles($user = null);
  abstract protected function fileMetadata($file, $user = null);

  function cleanup() {}
  function buildSettingsForm(&$form) {}
  function defaultSettings(&$defaults) {}
  function saveSettings($values) {}
  function validateSettings(&$form) { return true;}

  function buildFieldSettingsForm(&$form, &$fieldNames) {}
  function saveFieldSettings(&$form, $fieldId) {}
  function validateFieldSettings( $formName, &$fields, &$files, &$form, &$errors ) {return true;}
  function runForm( &$form ) {}

  static function getBackendService() {
    $backend_service_class = CRM_Core_BAO_Setting::getItem("securefiles", "securefiles_backend_service", null, "CRM_Securefiles_AmazonS3");

    if(class_exists($backend_service_class)) {
      return new $backend_service_class();
    } else {
      return false;
    }
  }

}