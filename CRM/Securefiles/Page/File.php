<?php

class CRM_Securefiles_Page_File extends CRM_Core_Page_File {

  /**
   * This function overrides CRM_CorePage_File::run
   * If we are trying to access a SecureFile offsite
   * we run this function. If we determine that we
   * aren't retrieving this file, fallback on the
   * core run function: parent::run()
   *
   * It is used for Download/View and Delete
   *
   */
  public function run() {
    $id = CRM_Utils_Request::retrieve('id', 'Positive', $this, TRUE);
    $eid = CRM_Utils_Request::retrieve('eid', 'Positive', $this, TRUE);
    $runParent = true;

    if(is_numeric($id)) {
      $file = civicrm_api3('File', 'getsingle', array(
        'id' => $id,
      ));

      $details = json_decode($file['description']);

      if ($details && property_exists($details, "source") && $details->source == "securefiles") {
        $action = CRM_Utils_Request::retrieve('action', 'String', $this);

        //Check Extension level permissions
        CRM_Securefiles_Permission::checkFilePerms(CRM_Core_Action::ADD, $id, $eid);

        $backendService = CRM_Securefiles_Backend::getBackendService();
        if ($backendService) {
          if (!($action & CRM_Core_Action::DELETE)) {
            //Check backend service permissions
            if ($backendService->checkPermissions(CRM_Core_Action::VIEW, $id, $eid) !== false) {
              $content = $backendService->downloadFile($file['uri'], $eid);
              CRM_Utils_System::download($file['uri'], $file['mime_type'], $content);
            } else {
              return null;
            }
          }

          //Skip delegating back to the core file handler
          $runParent = false;
        }
      }
    }

    if($runParent) {
      parent::run();
    }

  }

}
