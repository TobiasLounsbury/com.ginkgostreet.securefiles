<?php

require_once("aws/aws-autoloader.php");

use GuzzleHttp\Promise;
use GuzzleHttp\Promise\RejectedPromise;
use Aws\Credentials\CredentialProvider;
use Aws\Credentials\Credentials;
use Aws\Exception\CredentialsException;


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
          new Credentials($key, $secret)
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

  function getSTSToken() {
    $config = $this->getConfig();

    $key = $config["securefiles_s3_sts_key"];
    $secret = $config["securefiles_s3_sts_secret"];

    $stsClient = new \Aws\Sts\StsClient([
      'region' => 'us-east-1',
      'version' => '2011-06-15',
      'credentials' => [
        'key'    => $key,
        'secret' => $secret
      ]
    ]);

    $results = $stsClient->getSessionToken();
    $creds = $results['Credentials'];
    return $creds;
  }

  /*------------[ Below are The general Class functions for saving/loading and listing files ]------------*/


  public function uploadFile($file, $user = null) {
    $s3 = $this->getS3Client();
    //todo: create key
    //todo: Get metadata
    //todo: Handle server-side encryption settings
    //$s3->putObject();
    //todo: delete temp files
  }
  public function downloadFile($file, $user = null) {
    $config = $this->getConfig();
    $s3 = $this->getS3Client();
    try {
      $result = $s3->getObject(array(
        'Bucket' => $config['securefiles_s3_bucket'],
        'Key' => $file
      ));
    } catch (Exception $e) {
      error_log($e);
      return null;
    }

    return $result['Body'];
  }

  public function deleteFile($file, $user = null) {
    $s3 = $this->getS3Client();
    $config = $this->getConfig();
    return $s3->deleteObject(array(
      'Bucket' => $config['securefiles_s3_bucket'],
      'Key' => $file
    ));
  }

  public function listFiles($user = null) {
    $s3 = $this->getS3Client();
    $config = $this->getConfig();
    //'Prefix' is used for specifying folder
    $params = array("Bucket" => $config['securefiles_s3_bucket']);
    if(!is_null($user)) {
      $params['Prefix'] = $user."/";
    }
    return $s3->listObjects($params);
  }
  public function fileMetadata($file, $field = null) {
    $s3 = $this->getS3Client();
    $config = $this->getConfig();
    try {
      $response = $s3->headObject(array(
        'Bucket' => $config['securefiles_s3_bucket'],
        'Key'    => $file
      ));
    } catch(Exception $e) {
      error_log($e);
      return null;
    }

    if(!is_null($field)) {
        return $response[$field];
    }
    return $response;
  }







  function runForm(&$form, &$clientSideVars, $fields) {
    CRM_Core_Resources::singleton()->addScriptFile('com.ginkgostreet.securefiles', 'js/securefiles_widget_amazon_s3.js', 20, 'page-footer');

    $config = $this->getConfig();
    $clientSideVars['useSTS'] = ($config['securefiles_s3_use_sts'] == 1);
    if($config['securefiles_s3_use_sts']) {
      CRM_Core_Resources::singleton()->addScriptFile('com.ginkgostreet.securefiles', 'js/aws-sdk-2.2.26.min.js', 18, 'page-footer');
      $clientSideVars['Credentials'] = $this->getSTSToken();
      $clientSideVars['S3Region'] = $config['securefiles_s3_region'];
      $clientSideVars['S3Bucket'] = $config['securefiles_s3_bucket'];
      $clientSideVars['useEncryption'] = ($config['securefiles_s3_use_encryption'] == 1);;
      $clientSideVars['encryptionType'] = $config['securefiles_s3_encryption_type'];


      //Add the field configs
      $fieldConfigs = array();
      foreach($fields as $index => $fieldId) {
        $fieldName = $form->_elements[$index]->_attributes['name'];
        $fieldConfigs[$fieldName] = array();
        $fieldConfigs[$fieldName]['id'] = $fieldId;
        $fieldConfigs[$fieldName]['name'] = $fieldName;
        $fieldConfigs[$fieldName]['filename'] = CRM_Core_BAO_Setting::getItem("securefiles_s3_fields", "securefiles_s3_".$fieldId."_always_filename", false);
      }

      $clientSideVars['S3Fields'] = $fieldConfigs;
    }
  }

  function validateWidgetForm($metadata, $formName, &$fields, &$files, &$form, &$errors) {}

  function postProcessWidgetForm($metadata, $formName, &$form) {

    foreach($metadata as $fieldMetadata) {
      //Get the custom value
      $fieldId = str_replace("custom_", "", $fieldMetadata->field);
      $fieldId = preg_replace('/_.*/', "", $fieldId);
      $params = array(
        'entity_id' => $form->getVar("_contactId"),
      );

      $result = civicrm_api3('CustomValue', 'get', $params);


      $fileMetadata = $this->fileMetadata($fieldMetadata->name, "LastModified");

      $params = array(
        "mime_type" => $fieldMetadata->mime_type,
        "uri" => $fieldMetadata->name,
        "description" => '{"source": "securefiles"}',
        "upload_date" => $fileMetadata->format("Y-m-d h:m:s")
      );

      if($result['count'] > 0 && array_key_exists($fieldId, $result['values'])) {
        $params['id'] = $result['values'][$fieldId]['latest'];
      }

      //save the file entry
      $result = civicrm_api3('File', 'create', $params);


      if(!array_key_exists("id", $params)) {
        //Update the custom value lookup if needed
        $params = array(
          'custom_'.$fieldId => $result['values'][0]['id'],
          'entity_id' => $form->getVar("_contactId"),
        );
        $result = civicrm_api3('CustomValue', 'create', $params);
      }

    }
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

    $regions = array(
      "us-east-1" => "us-east-1: US East (N. Virginia)",
      "us-west-2" => "us-west-2: US West (Oregon)",
      "us-west-1" => "us-west-1: US West (N. California)",
      "eu-west-1" => "eu-west-1: EU (Ireland)",
      "eu-central-1" => "eu-central-1: EU (Frankfurt)",
      "ap-southeast-1" => "ap-southeast-1: Asia Pacific (Singapore)",
      "ap-southeast-2" => "ap-southeast-2: Asia Pacific (Sydney)",
      "ap-northeast-1" => "ap-northeast-1: Asia Pacific (Tokyo)",
      "sa-east-1" => "sa-east-1: South America (Sao Paulo)"
    );


    $form->add(
      'select', // field type
      'securefiles_s3_region', // field name
      ts('Amazon S3 Region'), // field label
      $regions,
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
      ts('Encryption Method'), // field label
      array("AES256" => "AES256", "aws:kms" => "aws:kms"),
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

    $form->add(
      'text', // field type
      'securefiles_s3_sts_key', // field name
      ts('Amazon S3 API Key for Client-side Requests'), // field label
      array("size" => 75),
      false // is required
    );

    $form->add(
      'text', // field type
      'securefiles_s3_sts_secret', // field name
      ts('Amazon S3 API Secret for Client-side Requests'), // field label
      array("size" => 75),
      false // is required
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
    CRM_Core_BAO_Setting::setItem((array_key_exists("securefiles_s3_sts_key", $values) ? $values['securefiles_s3_sts_key'] : ""),"securefiles_s3", "securefiles_s3_sts_key");
    CRM_Core_BAO_Setting::setItem((array_key_exists("securefiles_s3_sts_secret", $values) ? $values['securefiles_s3_sts_secret'] : ""),"securefiles_s3", "securefiles_s3_sts_secret");
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
  function validateSettings(&$form) {
    $valid = true;

    //If using STS, make sure we have the key and secret
    if (array_key_exists("securefiles_s3_use_sts", $form->_submitValues) && $form->_submitValues['securefiles_s3_use_sts']) {

      if (!array_key_exists("securefiles_s3_sts_key", $form->_submitValues) || !$form->_submitValues['securefiles_s3_sts_key']) {
        $form->_errors["securefiles_s3_sts_key"] = "When using client side tokens, the clientkey is required";
        $valid = false;
      }
      if (!array_key_exists("securefiles_s3_sts_secret", $form->_submitValues) || !$form->_submitValues['securefiles_s3_sts_secret']) {
        $form->_errors["securefiles_s3_sts_secret"] = "When using client side tokens, the clientkey is required";
        $valid = false;
      }

    }

    return $valid;
  }







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


  function saveFieldSettings(&$form, $fieldId) {
    CRM_Core_BAO_Setting::setItem((array_key_exists("securefiles_s3_always_filename", $form->_submitValues) ? $form->_submitValues['securefiles_s3_always_filename'] : ""),"securefiles_s3_fields", "securefiles_s3_".$fieldId."_always_filename");
  }

}