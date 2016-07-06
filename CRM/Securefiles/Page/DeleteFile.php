<?php

class CRM_Securefiles_Page_DeleteFile extends CRM_Core_Page_File {

  public function run() {

    $entityID = CRM_Utils_Request::retrieve('entityID', 'Positive', CRM_Core_DAO::$_nullObject, TRUE);
    $fileID = CRM_Utils_Request::retrieve('fileID', 'Positive', CRM_Core_DAO::$_nullObject, TRUE);

    $file = false;

    if(is_numeric($fileID)) {
      $file = civicrm_api3('File', 'getsingle', array(
        'id' => $fileID,
      ));
    }

    try {
      CRM_Core_BAO_File::deleteAttachment();
    }  catch(Exception $e) {
      return null;
    }

    //Do the backend-service delete

    if ($file && !empty($file['description'])) {
      $details = json_decode($file['description']);

      if ($details && property_exists($details, "source") && $details->source == "securefiles") {

        //Check extension level permissions
        CRM_Securefiles_Permission::checkFilePerms(CRM_Core_Action::DELETE, $fileID, $entityID);

        $backendService = CRM_Securefiles_Backend::getBackendService();
        if ($backendService) {
          //Check backend service permissions
          if ($backendService->checkPermissions(CRM_Core_Action::DELETE, $fileID, $entityID) !== false) {
            $backendService->deleteFile($file['uri'], $entityID);
          }
        }
      }
    }

  }

}