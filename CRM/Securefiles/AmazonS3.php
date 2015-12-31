<?php

require_once("aws/aws-autoloader.php");

use GuzzleHttp\Promise;
use GuzzleHttp\Promise\RejectedPromise;
use Aws\Credentials\CredentialProvider;

class CRM_Securefiles_AmazonS3 extends CRM_Securefiles_Backend {

  private $S3;
  private $CONFIG;
  private $CredentialProvider;


  /*------------[ S3 Specific Functions ]------------*/

  /**
   * Returns the Config settings
   *
   * @return mixed
   */
  function getConfig() {
    if(!$this->CONFIG) {
      $this->CONFIG = CRM_Core_BAO_Setting::getItem("securefiles_s3");
    }
    return $this->CONFIG;
  }

  /**
   * This function creates an AWS credential provider
   *
   * @return callable
   */
  private function getCredentials() {
    return function () {

      $config = $this->getConfig();

      $key = $config["securefiles_s3_key"];
      $secret = $config["securefiles_s3_secret"];

      if ($key && $secret) {
        return Promise\promise_for(
          new Credentials($key, $secret, getenv(self::ENV_SESSION))
        );
      }

      $msg = 'Could not retrieve credentials';
      return new RejectedPromise(new CredentialsException($msg));
    };
  }

  /**
   * This function keeps state for the credential provider
   * @return mixed
   */
  private function getCredentialProvider() {
    if($this->CredentialProvider) {
      return $this->CredentialProvider;
    }

    $provider = $this->getCredentials();
    $this->CredentialProvider = CredentialProvider::memoize($provider);

    return $this->CredentialProvider;
  }

  /**
   * This method returns an instance of the Amazon S3 Client SDK
   *
   * @return \Aws\S3\S3Client
   */
  function getS3Client() {
    if($this->S3) {
      return $this->S3;
    }

    $config = $this->getConfig();

    $this->S3 = new Aws\S3\S3Client([
      'version' => 'latest',
      'region'  => $config['securefiles_s3_region'],
      'credentials' => $this->getCredentialProvider()
    ]);

    return $this->S3;
  }


  /*------------[ Below are The general Class functions for saving/loading and listing files ]------------*/


  public function uploadFile($file, $user = null) {
    $s3 = $this->getS3Client();
    //$s3->putObject();
  }
  public function downloadFile($file, $user = null) {
    $s3 = $this->getS3Client();
    //$s3->getObject();
    return "This is a test";
  }
  public function deleteFile($file, $user = null) {
    $s3 = $this->getS3Client();
    //$s3->deleteObject();
  }
  public function listFiles($user = null) {
    $s3 = $this->getS3Client();
    //'Prefix' is used for specifying folder
    //$s3->listObjects();
  }
  public function fileMetadata($file, $user = null) {}



  function runForm(&$form, &$clientSideVars) {
    CRM_Core_Resources::singleton()->addScriptFile('com.ginkgostreet.securefiles', 'js/aws-sdk-2.2.26.min.js', 18, 'page-footer');
    CRM_Core_Resources::singleton()->addScriptFile('com.ginkgostreet.securefiles', 'js/securefiles_widget_amazon_s3.js', 20, 'page-footer');
  }


  /*------------[ Below are functions for working with the Settings form. ]------------*/

  /**
   * This form allows the Backend Service to
   * add custom fields for its configuration
   * to the SecureFiles settings form
   *
   * Delegated from CRM_Securefiles_Form_Settings::buildQuickForm
   *
   * @param $form
   * An instance of the Settings form being created
   */
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
    CRM_Core_Resources::singleton()->addScriptFile('com.ginkgostreet.securefiles', 'js/securefiles_settings_amazon_s3.js', 20, 'page-footer');
  }

  /**
   * This functions returns the values for the settings form
   *
   * Delegated from CRM_Securefiles_Form_Settings::setDefaultValues
   *
   * @param $defaults
   */
  function defaultSettings(&$defaults) {
    $defaults = array_merge($defaults, CRM_Core_BAO_Setting::getItem("securefiles_s3"));
    $defaults['securefiles_s3_use_encryption'] = CRM_Core_BAO_Setting::getItem("securefiles_s3", "securefiles_s3_use_encryption", null, 1);
  }

  /**
   * This function saves the values submitted via
   *  The SecureFiles settings form
   *
   *  Delegated from CRM_Securefiles_Form_Settings::postProcess
   *
   * @param $values
   *  The values submitted via the Securefiles settings form
   */
  function saveSettings($values) {
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_region'],"securefiles_s3", "securefiles_s3_region");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_key'],"securefiles_s3", "securefiles_s3_key");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_secret'],"securefiles_s3", "securefiles_s3_secret");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_bucket'],"securefiles_s3", "securefiles_s3_bucket");
    CRM_Core_BAO_Setting::setItem((array_key_exists("securefiles_s3_use_encryption", $values) ? $values['securefiles_s3_use_encryption'] : 0),"securefiles_s3", "securefiles_s3_use_encryption");
    CRM_Core_BAO_Setting::setItem($values['securefiles_s3_encryption_type'],"securefiles_s3", "securefiles_s3_encryption_type");
    CRM_Core_BAO_Setting::setItem((array_key_exists("securefiles_s3_use_sts", $values) ? $values['securefiles_s3_use_sts'] : 0),"securefiles_s3", "securefiles_s3_use_sts");
  }

  /**
   * This form validates the settings specific to this
   * Backend Service
   *
   * Delegated from CRM_Securefiles_Form_Settings::validate
   *
   * @param $form
   * An instance of the form that is being validated
   * @return bool
   * Whether this form is valid
   */
  function validateSettings(&$form) { return true;}







  /*---------[ Below are the functions for modifying the individual field settings ]---------*/


  /**
   * Add the filename field to the form.
   *
   * @param $form
   * An instance of the form being created
   * @param $fieldNames
   * An ordered array of the name of all fields
   * the template should render.
   */
  function buildFieldSettingsForm(&$form, &$fieldNames) {
    $form->add(
      'text', // field type
      'securefiles_s3_always_filename', // field name
      ts('Always save this file with name?'), // field label
      array("size" => 50),
      false // is required
    );
    $fieldNames[] = "securefiles_s3_always_filename";
  }

}