<?php

class CRM_Securefiles_AmazonS3 extends CRM_Securefiles_Backend {

  protected function uploadFile($file, $user = null) {}
  protected function downloadFile($file, $user = null) {}
  protected function deleteFile($file, $user = null) {}
  protected function listFiles($user = null) {}
  protected function fileMetadata($file, $user = null) {}

  function buildSettingsForm(&$form) {}
  function defaultSettings(&$defaults) {}
  function saveSettings($values) {}
  function validateSettings(&$form) { return true;}

}