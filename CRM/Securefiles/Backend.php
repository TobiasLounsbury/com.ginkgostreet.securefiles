<?php

abstract class CRM_Securefiles_Backend {

  abstract protected function uploadFile($file, $user = null);
  abstract protected function downloadFile($file, $user = null);
  abstract protected function listFiles($user = null);
  abstract protected function fileMetadata($file, $user = null);

  function cleanup() {}
  function buildSettingsForm(&$form) {}
  function defaultSettings(&$defaults) {}
  function saveSettings($values) {}
  function validateSettings(&$form) { return true;}
}