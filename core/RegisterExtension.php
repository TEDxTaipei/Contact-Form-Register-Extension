<?php

namespace TEDxTaipei\RegisterExtension;

class RegisterExtension
{
  public static $ABSPATH = null;
  private static $instance = null;

  private static $dependencies = array();
  private $enable = true;
  private $notice = array();

  /**
   * Consturct
   */

  private function __construct()
  {
    self::$ABSPATH = dirname(dirname(__FILE__));

    // Register Dependencies Plugin
    array_push(self::$dependencies, array('contact-form-7-to-database-extension', 'contact-form-7-db.php'));

    // Register WordPress Hooks
    add_action('admin_notices', array(&$this, 'displayNotice'));
    add_action('plugins_loaded', array(&$this, 'loadTextdomain'));
  }

  /**
   * Get Instance
   *
   * @return object
   */

  public static function getInstance()
  {
    if(empty(self::$instance)) {
      self::$instance = new RegisterExtension;
    }

    return self::$instance;
  }

  /**
   * Bootstrap
   *
   * Initialize Plugin
   */

  public function bootstrap()
  {
    add_action('admin_init', array(&$this, 'checkDependencies'));

    add_action('wpcf7_validate_email*', array(&$this, 'checkEMailIsUnique'), 10, 2);
  }

  /**
   * Check Dependencies
   *
   * @return boolean
   */

  public function checkDependencies()
  {
    $dependenciaStatus = array();
    $dependenciaCheck = true;

    foreach(self::$dependencies as $dependencia) {
      $dependenciaStatus[$dependencia[0]] = is_plugin_active(implode('/', $dependencia));
      $dependenciaCheck = $dependenciaCheck && $dependenciaStatus[$dependencia[0]];
    }


    if(!$dependenciaCheck) {
      $dependPlugin = key($dependenciaStatus);
      $this->makeNotice('error', sprintf(__('<strong>Contact From Register Extension:</strong> Dependencies plguin %s is not active.', 'tedxtaipei'), $dependPlugin));
    }

    $this->enable = $dependenciaCheck;

  }

  /**
   * Check Email Is Unique
   *
   * @var array
   * @var array
   */

  public function checkEMailIsUnique($result, $tag = array())
  {
    $fieldName = "unique-email";
    $name = $tag['name'];

    if($name === $fieldName) {
      $value = $_POST[$name];
      $formID = $_POST['_wpcf7'];
      if(!$this->checkEMailUniqueInDatabase($value, $name, $formID)) {
        $result['valid'] = false;
        $result['reason'][$name] = __("Your email is already exists.", 'tedxtaipei');
      }
    }

    return $result;
  }

  /**
   * Check Email Unique In Database
   *
   * @var string
   * @var string
   * @var integer
   */

  private function checkEMailUniqueInDatabase($email, $fieldName, $formID)
  {
    require_once self::$ABSPATH . '/../contact-form-7-to-database-extension/CFDBFormIterator.php';

    $form = get_post($formID);
    $formName = $form->post_title;

    $CFDB = new \CFDBFormIterator();
    $atts = array(
      'show' => $fieldName,
      'filter' => "$fieldName=$email"
    );

    $CFDB->export($formName, $atts);
    $unique = true;

    while($row = $CFDB->nextRow()) {
      $unique = false;
    }

    return $unique;
  }


  /**
   * Make Notice
   *
   * @var string
   * @var string
   */

  private function makeNotice($type, $message)
  {
    array_push($this->notice, array('type' => $type, 'message' => $message));
  }

  /**
   * Display Notice
   */

  public function displayNotice()
  {
    foreach($this->notice as $notice) {
      echo "<div class=\"{$notice['type']}\"><p>";
      echo $notice['message'];
      echo "</p></div>";
    }
  }

  /**
   * Load Textdomain
   */

  public function loadTextdomain()
  {
    load_plugin_textdomain('tedxtaipei', false, basename(self::$ABSPATH) . '/lang');
  }

}
