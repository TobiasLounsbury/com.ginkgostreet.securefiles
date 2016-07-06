<?php
return array(
  'securefiles_backend_service' => array(
    'group_name' => 'CiviCRM Preferences',
    'group' => 'securefiles',
    'name' => 'securefiles_backend_service',
    'type' => 'String',
    'default' => 'CRM_Securefiles_AmazonS3',
    'add' => '4.4',
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => 'Backend Service',
    'help_text' => 'The backend service you want secure file storage to use',
  )
);