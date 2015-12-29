<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Securefiles_Form_Settings extends CRM_Core_Form {

  private $backend_service_class;
  private $backend_service = false;

  /**
   * set variables up before form is built
   *
   * @access public
   */
  public function preProcess() {
    parent::preProcess();
    //Set the Backend Service in use currently.
    $this->backend_service_class = CRM_Core_BAO_Setting::getItem("securefiles", "securefiles_backend_service", null, "CRM_Securefiles_AmazonS3");
    $this->backend_service = new $this->backend_service_class();
  }

  /**
   * Set default values for the form. For edit/view mode
   * the default values are retrieved from the database
   *
   * @access public
   *
   * @return array
   */
  function setDefaultValues() {
    $defaults =  array();
    $defaults['securefiles_backend_service'] = $this->backend_service_class;
    //Allow the backend service to set defaults
    $this->backend_service->defaultSettings($defaults);
    return $defaults;
  }

  function buildQuickForm() {

    // add form elements
    $this->add(
      'select', // field type
      'securefiles_backend_service', // field name
      'Backend Service Provider', // field label
      CRM_Securefiles_Hooks::getBackendServices(), // list of options
      true // is required
    );

    //Allow the Backend service to add fields
    $this->backend_service->buildSettingsForm($this);

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Save Settings'),
        'isDefault' => TRUE,
      ),
    ));

    //Add our JS to the Page
    //todo: Add js to warn about changing backend

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * Handle the data submitted
   */
  function postProcess() {
    $values = $this->exportValues();

    if ($values['securefiles_backend_service'] != $this->backend_service_class) {
      CRM_Core_BAO_Setting::setItem($values['securefiles_backend_service'],"securefiles", "securefiles_backend_service");
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/securefiles/settings'));
      return;
    }

    $this->backend_service->saveSettings($values);

    parent::postProcess();
  }

  function validate() {
    return $this->backend_service->validateSettings($this);
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
