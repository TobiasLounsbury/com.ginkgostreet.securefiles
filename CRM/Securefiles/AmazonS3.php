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

    $form->add(
      'checkbox',
      'securefiles_s3_use_encryption',
      ts('Encrypt Stored Files')
    );

    $form->add(
      'select', // field type
      'securefiles_s3_encryption_type', // field name
      'Encryption Method', // field label
      array("SSE-S3" => "SSE-S3", "SSE-KMS" => "SSE-KMS", "SSE-C" => "SSE-C"),
      true // is required
    );

    $form->add(
      'text', // field type
      'securefiles_s3_encryption_cert', // field name
      ts('Path to Custom Encryption Cert'), // field label
      array("size" => 75),
      false // is required
    );

    //Checkbox to allow using Temp auth to upload files rather than
    //use the default config
    //This allows the files to never touch the server.
    $form->add(
      'checkbox',
      'securefiles_s3_use_sts',
      ts('Use Client-side Auth Tokens')
    );


    //Add Amazon S3 specific settings JS
    CRM_Core_Resources::singleton()->addScriptFile('com.ginkgostreet.securefiles', 'js/securefiles_amazon_s3.js', 20, 'page-footer');
  }

  function defaultSettings(&$defaults) {
    $defaults = array_merge($defaults, CRM_Core_BAO_Setting::getItem("securefiles_s3"));
    $defaults['securefiles_s3_use_encryption'] = CRM_Core_BAO_Setting::getItem("securefiles_s3", "securefiles_s3_use_encryption", null, 1);
  }

  function saveSettings($values) {
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_region'],"securefiles_s3", "securefiles_s3_region");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_key'],"securefiles_s3", "securefiles_s3_key");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_secret'],"securefiles_s3", "securefiles_s3_secret");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_bucket'],"securefiles_s3", "securefiles_s3_bucket");
    CRM_Core_BAO_Setting::setItem((array_key_exists("securefiles_s3_use_encryption", $values) ? $values['securefiles_s3_use_encryption'] : 0),"securefiles_s3", "securefiles_s3_use_encryption");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_encryption_type'],"securefiles_s3", "securefiles_s3_encryption_type");
    CRM_Core_BAO_Setting::setItem((array_key_exists("securefiles_s3_use_sts", $values) ? $values['securefiles_s3_use_sts'] : 0),"securefiles_s3", "securefiles_s3_use_sts");
  }
  function validateSettings(&$form) { return true;}

}