<?php

class CRM_Securefiles_AmazonS3 extends CRM_Securefiles_Backend {

  protected function uploadFile($file, $user = null) {}
  protected function downloadFile($file, $user = null) {}
  protected function deleteFile($file, $user = null) {}
  protected function listFiles($user = null) {}
  protected function fileMetadata($file, $user = null) {}

  function buildSettingsForm(&$form) {
    $form->add(
      'text', // field type
      'securefiles_s3_region', // field name
      ts('Amazon S3 Region'), // field label
      array("size" => 25),
      true // is required
    );

    $form->add(
      'text', // field type
      'securefiles_s3_key', // field name
      ts('Amazon S3 API Key'), // field label
      array("size" => 75),
      true // is required
    );

    $form->add(
      'text', // field type
      'securefiles_s3_secret', // field name
      ts('Amazon S3 API Secret'), // field label
      array("size" => 75),
      true // is required
    );

    $form->add(
      'text', // field type
      'securefiles_s3_bucket', // field name
      ts('Amazon S3 Bucket Name'), // field label
      array("size" => 50),
      true // is required
    );

  }
  function defaultSettings(&$defaults) {
    $defaults = array_merge($defaults, CRM_Core_BAO_Setting::getItem("securefiles_s3"));
  }
  function saveSettings($values) {
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_region'],"securefiles_s3", "securefiles_s3_region");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_key'],"securefiles_s3", "securefiles_s3_key");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_secret'],"securefiles_s3", "securefiles_s3_secret");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_bucket'],"securefiles_s3", "securefiles_s3_bucket");
  }
  function validateSettings(&$form) { return true;}

}